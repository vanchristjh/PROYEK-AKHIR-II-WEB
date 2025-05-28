@extends('layouts.dashboard')

@section('title', 'Manajemen Event')

@section('header', 'Manajemen Event')

@section('navigation')
    <li>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.events.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-calendar-alt text-lg w-6"></i>
            <span class="ml-3">Event</span>
        </a>
    </li>
@endsection

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Event</h2>
        <a href="{{ route('admin.events.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center transition-colors duration-200">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Event Baru
        </a>
    </div>
    
    @if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    @if (session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    @if(count($events) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Judul</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Tanggal Mulai</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Tanggal Selesai</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Lokasi</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Target</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Status</th>
                    <th class="p-3 border-b text-sm font-medium text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border-b">{{ $event->title }}</td>
                    <td class="p-3 border-b">{{ $event->start_date->format('d M Y H:i') }}</td>
                    <td class="p-3 border-b">{{ $event->end_date->format('d M Y H:i') }}</td>
                    <td class="p-3 border-b">{{ $event->location ?? '-' }}</td>
                    <td class="p-3 border-b">
                        @if($event->audience == 'all')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Semua</span>
                        @elseif($event->audience == 'teachers')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Guru</span>
                        @elseif($event->audience == 'students')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Siswa</span>
                        @elseif($event->audience == 'admin')
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Admin</span>
                        @endif
                    </td>
                    <td class="p-3 border-b">
                        @if($event->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="p-3 border-b">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="text-blue-500 hover:text-blue-700" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" class="inline" onsubmit="return confirm('Anda yakin ingin menghapus event ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $events->links() }}
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded">
        <p class="text-yellow-700">Belum ada event yang ditambahkan.</p>
    </div>
    @endif
</div>
@endsection
