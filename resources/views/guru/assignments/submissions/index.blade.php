@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengumpulan Tugas: {{ $assignment->title }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('guru.assignments.submissions.export', $assignment) }}" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                <i class="mr-2 fas fa-file-excel"></i>Export Excel
            </a>
            <a href="{{ route('guru.assignments.submissions.export-pdf', $assignment) }}" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                <i class="mr-2 fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('guru.assignments.show', $assignment) }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </div>

    @if($submissions->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <p class="text-gray-500">Belum ada pengumpulan tugas.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengumpulan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($submissions as $index => $submission)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $submission->student->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $submission->created_at->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->is_late)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Terlambat
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Tepat Waktu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->score ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('guru.assignments.submissions.show', ['assignment' => $assignment->id, 'submission' => $submission->id]) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="mt-4">
                {{ $submissions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection