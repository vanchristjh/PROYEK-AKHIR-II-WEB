@extends('layouts.dashboard')

@section('title', 'Event Sekolah')

@section('header', 'Event Sekolah')

@section('navigation')
    <li>
        <a href="{{ route('siswa.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.events.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-calendar-alt text-lg w-6"></i>
            <span class="ml-3">Event</span>
        </a>
    </li>
@endsection

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Event Sekolah</h2>
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
        <div class="bg-white border rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow duration-300">
            @if($event->image_path)
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="h-32 bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-4xl text-indigo-400"></i>
                </div>
            @endif
            
            <div class="p-4">
                <div class="flex flex-wrap gap-2 mb-2">
                    @if($event->audience == 'all')
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Semua</span>
                    @elseif($event->audience == 'students')
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Siswa</span>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $event->title }}</h3>
                
                <div class="flex items-center text-sm text-gray-600 mb-3">
                    <i class="fas fa-clock mr-2"></i>
                    <span>{{ $event->start_date->format('d M Y') }}</span>
                </div>
                
                @if($event->location)
                <div class="flex items-center text-sm text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span>{{ $event->location }}</span>
                </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ route('siswa.events.show', $event->id) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="mt-6">
        {{ $events->links() }}
    </div>
    
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded">
        <p class="text-yellow-700">Tidak ada event yang tersedia saat ini.</p>
    </div>
    @endif
</div>
@endsection
