@extends('layouts.siswa')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('siswa.notifications.index') }}" class="text-blue-500 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Notifikasi
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="border-b pb-4 mb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $notification->title }}</h1>
            <div class="flex items-center text-sm text-gray-500 mt-2">
                <span class="mr-4">
                    <i class="far fa-clock mr-1"></i>{{ $notification->created_at->format('d M Y, H:i') }}
                </span>
                <span>
                    <i class="far fa-user mr-1"></i>Dari: {{ $notification->sender->name ?? 'Sistem' }}
                </span>
            </div>
        </div>

        <div class="prose max-w-none">
            <p>{{ $notification->message }}</p>
        </div>

        @if($notification->type === 'assignment_reminder' && isset($notification->data['assignment_id']))
        <div class="mt-6">
            <a href="{{ route('siswa.assignments.show', $notification->data['assignment_id']) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Lihat Tugas
            </a>
        </div>
        @endif
    </div>

    <div class="mt-6 flex justify-between">
        <form action="{{ route('siswa.notifications.destroy', $notification) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                Hapus Notifikasi
            </button>
        </form>

        @unless($notification->is_read)
        <form action="{{ route('siswa.notifications.mark-read', $notification) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Tandai Sudah Dibaca
            </button>
        </form>
        @endunless
    </div>
</div>
@endsection
