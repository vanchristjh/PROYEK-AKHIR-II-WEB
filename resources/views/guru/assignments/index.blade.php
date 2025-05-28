@extends('layouts.dashboard')

@section('title', 'Manajemen Tugas')

@section('header', 'Manajemen Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-5 -top-5 opacity-10">
            <i class="fas fa-tasks text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Manajemen Tugas</h2>
                    <p class="text-blue-100">Buat, lihat, dan kelola tugas untuk siswa</p>
                </div>
                <a href="{{ route('guru.assignments.create') }}" class="bg-white text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors">
                    <i class="fas fa-plus mr-2"></i> Buat Tugas Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Notification Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
        <form action="{{ route('guru.assignments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Tugas</label>
                <div class="relative rounded-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Judul atau deskripsi tugas..." 
                           class="block w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="subject" id="subject" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="class" id="class" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tugas
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mata Pelajaran
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kelas
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deadline
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengumpulan
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $assignment->title }}</div>
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ $assignment->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    {{ $assignment->subject->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($assignment->classes as $class)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                            {{ $class->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $deadlineDate = $assignment->deadline ?? $assignment->due_date;
                                    $isExpired = $deadlineDate && $deadlineDate->isPast();
                                @endphp
                                <div class="text-sm {{ $isExpired ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                    {{ $deadlineDate ? $deadlineDate->format('d M Y, H:i') : 'Tidak ada' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(!$assignment->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak Aktif
                                    </span>
                                @elseif($isExpired)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                        Kedaluwarsa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $assignment->submissions_count ?? 0 }} pengumpulan
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('guru.assignments.show', $assignment) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>                                    <a href="{{ route('guru.assignments.submissions.index', $assignment) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-clipboard-list"></i>
                                    </a>
                                    <a href="{{ route('guru.assignments.edit', $assignment) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($assignment->submissions_count == 0)
                                        <form action="{{ route('guru.assignments.destroy', $assignment) }}" method="POST" 
                                              class="inline-block delete-assignment-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-assignment-btn">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-medium">Belum ada tugas</p>
                                    <p class="text-gray-400 text-sm mt-1">Klik tombol "Buat Tugas Baru" untuk membuat tugas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($assignments->hasPages())
        <div class="px-4">
            {{ $assignments->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Add delete confirmation functionality
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-assignment-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-assignment-form');
                if(form && confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
