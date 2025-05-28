@extends('layouts.dashboard')

@section('title', 'Tambah Event Baru')

@section('header', 'Tambah Event Baru')

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
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.events.index') }}" class="flex items-center text-blue-500 hover:text-blue-700 mr-4">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
        <h2 class="text-xl font-semibold text-gray-800">Tambah Event Baru</h2>
    </div>
    
    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Terjadi kesalahan:</p>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Event <span class="text-red-600">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            </div>
            
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Mulai <span class="text-red-600">*</span></label>
                <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Selesai <span class="text-red-600">*</span></label>
                <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            </div>
            
            <div>
                <label for="audience" class="block text-sm font-medium text-gray-700 mb-1">Ditujukan Untuk <span class="text-red-600">*</span></label>
                <select name="audience" id="audience" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                    <option value="all" {{ old('audience') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="teachers" {{ old('audience') == 'teachers' ? 'selected' : '' }}>Guru</option>
                    <option value="students" {{ old('audience') == 'students' ? 'selected' : '' }}>Siswa</option>
                    <option value="admin" {{ old('audience') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="flex items-center space-x-4 mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" checked class="form-radio text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2">Aktif</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') === '0' ? 'checked' : '' }} class="form-radio text-blue-600 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="4" class="form-textarea w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
        </div>
        
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Gambar (Opsional)</label>
            <input type="file" name="image" id="image" accept="image/*" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <p class="text-xs text-gray-500 mt-1">Format yang diizinkan: JPG, PNG, GIF (Maks. 2MB)</p>
        </div>
        
        <div class="flex justify-end">
            <a href="{{ route('admin.events.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md mr-2">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
