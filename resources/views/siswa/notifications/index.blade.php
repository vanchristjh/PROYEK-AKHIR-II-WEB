@extends('layouts.siswa')

@section('title', 'Notifikasi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ route('siswa.notifications.mark-all-read') }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Tandai Semua Sudah Dibaca
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if($notifications->isEmpty())
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <div class="text-gray-500 my-8">
            <i class="fas fa-bell-slash text-4xl mb-4"></i>
            <p class="text-lg">Anda tidak memiliki notifikasi.</p>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <ul class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
            <li class="hover:bg-gray-50 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                <a href="{{ route('siswa.notifications.show', $notification) }}" class="block">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($notification->type == 'assignment_reminder')
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-book text-blue-500"></i>
                                    </div>
                                    @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-bell text-gray-500"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $notification->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($notification->message, 100) }}</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="text-sm text-gray-500 mr-6">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                                @unless($notification->is_read)
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                @endunless
                            </div>
                        </div>
                    </div>
                </a>
                <div class="flex px-6 py-2 bg-gray-50 border-t border-gray-100 text-xs">
                    <form action="{{ route('siswa.notifications.mark-read', $notification) }}" method="POST" class="mr-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="text-blue-500 hover:text-blue-700 {{ $notification->is_read ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $notification->is_read ? 'disabled' : '' }}>
                            <i class="fas fa-check mr-1"></i> Tandai Dibaca
                        </button>
                    </form>
                    <form action="{{ route('siswa.notifications.destroy', $notification) }}" method="POST" class="ml-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
