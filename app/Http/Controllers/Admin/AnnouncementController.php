<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Exports\AnnouncementExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Announcement::with('author');

        // Filter by importance
        if ($request->has('importance')) {
            if ($request->importance == 'important') {
                $query->where('is_important', true);
            } elseif ($request->importance == 'regular') {
                $query->where('is_important', false);
            }
        }

        // Filter by author
        if ($request->has('author') && $request->author) {
            $query->where('author_id', $request->author);
        }

        $announcements = $query->latest('publish_date')->paginate(10);
        $authors = User::whereHas('announcements')->get();

        return view('admin.announcements.index', compact('announcements', 'authors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|in:all,administrators,teachers,students',
            'is_important' => 'sometimes|boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            $data = $validatedData;
            $data['author_id'] = auth()->id();
            $data['is_important'] = $request->has('is_important');
            $data['publish_date'] = $data['publish_date'] ?? now();

            // Handle file upload if provided
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('announcements', 'public');
                $data['attachment'] = $attachmentPath;
                $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
            }

            // Create announcement
            $announcement = Announcement::create($data);

            // Log activity
            activity()
                ->performedOn($announcement)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $announcement->title])
                ->log('created announcement');

            DB::commit();

            return redirect()->route('admin.announcements.index')
                ->with('success', 'Pengumuman berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat pengumuman: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|in:all,administrators,teachers,students',
            'is_important' => 'sometimes|boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'attachment' => 'nullable|file|max:20000|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
            'remove_attachment' => 'sometimes|boolean'
        ]);

        // Set is_important to false if not provided
        $validatedData['is_important'] = $request->has('is_important');

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($announcement->attachment && Storage::disk('public')->exists($announcement->attachment)) {
                Storage::disk('public')->delete($announcement->attachment);
            }
            $validatedData['attachment'] = $request->file('attachment')
                ->store('announcements', 'public');
        } elseif ($request->has('remove_attachment') && $announcement->attachment) {
            // Remove attachment if requested
            Storage::disk('public')->delete($announcement->attachment);
            $validatedData['attachment'] = null;
        } else {
            // Keep the existing attachment
            unset($validatedData['attachment']);
        }

        $announcement->update($validatedData);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        // Delete attachment if exists
        if ($announcement->attachment && Storage::disk('public')->exists($announcement->attachment)) {
            Storage::disk('public')->delete($announcement->attachment);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Download the announcement attachment.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function download(Announcement $announcement)
    {
        if (!$announcement->attachment || !Storage::disk('public')->exists($announcement->attachment)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($announcement->attachment, $announcement->getAttachmentName());
    }

    /**
     * Export announcement to PDF
     *
     * @param  Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Announcement $announcement)
    {
        $pdf = PDF::loadView('admin.announcements.pdf', compact('announcement'));
        return $pdf->download('pengumuman-' . $announcement->id . '.pdf');
    }

    /**
     * Export announcement to Excel
     *
     * @param  Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Announcement $announcement)
    {
        return Excel::download(new AnnouncementExport($announcement), 'pengumuman-' . $announcement->id . '.xlsx');
    }
}
