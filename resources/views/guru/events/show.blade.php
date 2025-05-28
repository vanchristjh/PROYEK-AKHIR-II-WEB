@extends('layouts.dashboard')

@section('title', 'Detail Event')

@section('header', 'Detail Event')

@section('navigation')
    <li>
        <a href="{{ route('guru.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.events.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-calendar-alt text-lg w-6"></i>
            <span class="ml-3">Event</span>
        </a>
    </li>
@endsection

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('guru.events.index') }}" class="flex items-center text-blue-500 hover:text-blue-700 mr-4">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Event
        </a>
        <h2 class="text-xl font-semibold text-gray-800">Detail Event</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->title }}</h3>
                
                <div class="flex items-center text-gray-600 mb-4">
                    <i class="fas fa-clock mr-2"></i>
                    <span>{{ $event->start_date->format('d M Y H:i') }} - {{ $event->end_date->format('d M Y H:i') }}</span>
                </div>
                
                @if($event->location)
                <div class="flex items-center text-gray-600 mb-4">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span>{{ $event->location }}</span>
                </div>
                @endif
                
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($event->audience == 'all')
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            <i class="fas fa-users mr-1"></i> Untuk Semua
                        </span>
                    @elseif($event->audience == 'teachers')
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> Untuk Guru
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="border-t pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-3">Deskripsi</h4>
                <div class="prose max-w-none">
                    @if ($event->description)
                        {!! nl2br(e($event->description)) !!}
                    @else
                        <p class="text-gray-500 italic">Tidak ada deskripsi.</p>
                    @endif
                </div>
            </div>
            
            <div class="border-t pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-3">Informasi Tambahan</h4>
                <table class="w-full">
                    <tbody>
                        <tr>
                            <td class="py-2 text-gray-600">Tanggal dibuat</td>
                            <td>{{ $event->created_at->format('d M Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="md:col-span-1">
            @if ($event->image_path)
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h4 class="text-lg font-medium text-gray-900 mb-3">Gambar Event</h4>
                <div class="rounded-md overflow-hidden shadow">
                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-auto">
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="mt-8 pt-6 border-t">
        <a href="{{ route('guru.events.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md">
            Kembali ke Daftar Event
        </a>
    </div>
</div>
@endsection
