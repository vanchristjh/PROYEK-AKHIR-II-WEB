@extends('layouts.dashboard')

@section('title', 'Detail Jadwal')

@section('header', 'Detail Jadwal Pelajaran')

@section('navigation')
    @include('siswa.partials.sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('siswa.schedule.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center text-sm font-medium transition-all duration-200 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i> Kembali ke Daftar Jadwal
        </a>
    </div>
    
    <!-- Schedule Details -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200/80">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Detail Jadwal</h3>
                <div class="flex space-x-3">
                    <a href="{{ route('siswa.schedule.day', $schedule->day) }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-calendar-day mr-1.5"></i>
                        Lihat Jadwal Hari {{ $schedule->day }}
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left column: Schedule details -->
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Kelas</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->classroom ? $schedule->classroom->name : 'Tidak ditemukan' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Mata Pelajaran</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->subject ? $schedule->subject->name : 'Tidak ditemukan' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Guru</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->teacher_name }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Hari</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->day }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Waktu</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <span class="text-sm text-gray-500 font-medium">Ruangan</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->room ?: 'Tidak ditentukan' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-sm text-gray-500 font-medium">Tahun Ajaran</span>
                            <span class="text-sm font-semibold text-gray-800 bg-gray-50 px-3 py-1 rounded-md">
                                {{ $schedule->school_year ?: date('Y') . '/' . ((int)date('Y')+1) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($schedule->notes)
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan:</h4>
                        <p class="text-sm text-gray-600">{{ $schedule->notes }}</p>
                    </div>
                    @endif
                </div>
                
                <!-- Right column: Additional information -->
                <div>
                    <!-- Teacher information -->
                    @if($schedule->teacher)
                    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                        <h5 class="text-sm font-semibold text-gray-600 mb-2">Informasi Guru</h5>
                        <div class="flex items-center py-2 bg-blue-50 px-3 rounded-md">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-user-tie text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $schedule->teacher_name }}</div>
                                @if($schedule->teacher->email)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-envelope mr-1"></i> {{ $schedule->teacher->email }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Subject details if available -->
                    @if($schedule->subject)
                    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                        <h5 class="text-sm font-semibold text-gray-600 mb-2">Detail Mata Pelajaran</h5>
                        <div class="text-sm bg-gray-50 p-3 rounded-md">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="text-gray-500">Nama:</div>
                                <div class="font-medium">{{ $schedule->subject->name }}</div>
                                
                                @if($schedule->subject->code)
                                <div class="text-gray-500">Kode:</div>
                                <div class="font-medium">{{ $schedule->subject->code }}</div>
                                @endif
                                
                                @if($schedule->subject->description)
                                <div class="text-gray-500 col-span-2 mt-2">Deskripsi:</div>
                                <div class="font-medium col-span-2">{{ $schedule->subject->description }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related materials if available -->
    @php
        $materials = $schedule->subject ? 
            \App\Models\Material::where('subject_id', $schedule->subject->id)
                ->where('is_active', true)
                ->where(function($q) use ($schedule) {
                    $q->whereNull('classroom_id')
                      ->orWhere('classroom_id', $schedule->classroom_id);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get() : 
            collect([]);
    @endphp
    
    @if($materials->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Materi Pelajaran Terkait</h3>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-blue-50">
                <h4 class="font-medium text-gray-700">
                    <i class="fas fa-book-open mr-2 text-blue-500"></i> Materi {{ $schedule->subject->name }}
                </h4>
            </div>
            
            <div class="p-4">
                <ul class="divide-y divide-gray-100">
                    @foreach($materials as $material)
                    <li class="py-3 hover:bg-gray-50 px-2 rounded-md transition-colors">
                        <a href="{{ route('siswa.material.show', $material) }}" class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-lg bg-{{ $material->file_extension == 'pdf' ? 'red' : ($material->file_extension == 'ppt' || $material->file_extension == 'pptx' ? 'orange' : 'blue') }}-100 flex items-center justify-center text-{{ $material->file_extension == 'pdf' ? 'red' : ($material->file_extension == 'ppt' || $material->file_extension == 'pptx' ? 'orange' : 'blue') }}-500">
                                    <i class="fas {{ $material->file_extension == 'pdf' ? 'fa-file-pdf' : ($material->file_extension == 'ppt' || $material->file_extension == 'pptx' ? 'fa-file-powerpoint' : 'fa-file-alt') }}"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $material->title }}</p>
                                <p class="text-xs text-gray-500">
                                    <span>{{ \Carbon\Carbon::parse($material->created_at)->format('d M Y') }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ strtoupper($material->file_extension) }}</span>
                                </p>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('siswa.material.index', ['subject_id' => $schedule->subject_id]) }}" class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center">
                        <span>Lihat semua materi</span>
                        <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Assignments related to this subject if available -->
    @php
        $assignments = $schedule->subject ? 
            \App\Models\Assignment::where('subject_id', $schedule->subject->id)
                ->where(function($q) use ($schedule) {
                    $q->whereNull('classroom_id')
                      ->orWhere('classroom_id', $schedule->classroom_id);
                })
                ->whereDate('deadline', '>=', now())
                ->orderBy('deadline')
                ->take(3)
                ->get() : 
            collect([]);
    @endphp
    
    @if($assignments->count() > 0)
    <div class="mt-8 mb-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Tugas Terkait</h3>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="p-4 border-b border-gray-200 bg-yellow-50">
                <h4 class="font-medium text-gray-700">
                    <i class="fas fa-tasks mr-2 text-yellow-500"></i> Tugas {{ $schedule->subject->name }} yang Akan Datang
                </h4>
            </div>
            
            <div class="p-4">
                @foreach($assignments as $assignment)
                <div class="mb-3 border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <a href="{{ route('siswa.assignments.show', $assignment) }}" class="block">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900">{{ $assignment->title }}</h5>
                                <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($assignment->description, 100) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="far fa-clock mr-1"></i> Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
                
                <div class="mt-4 text-center">
                    <a href="{{ route('siswa.assignments.index', ['subject_id' => $schedule->subject_id]) }}" class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center">
                        <span>Lihat semua tugas</span>
                        <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
