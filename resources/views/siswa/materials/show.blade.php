@extends('layouts.dashboard')

@section('title', $material->title)

@section('header', 'Detail Materi Pembelajaran')

@section('navigation')
    @include('siswa.partials.sidebar')
@endsection

@section('styles')
<style>
    /* Modern Color Palette */
    :root {
        --primary: #4361ee;
        --primary-light: #eef2ff;
        --primary-dark: #3a56d4;
        --secondary: #2d3748;
        --accent: #4cc9f0;
        --success: #06d6a0;
        --info: #118ab2;
        --warning: #ffd166;
        --danger: #ef476f;
        --light: #f8fafc;
        --dark: #1a202c;
        --white: #ffffff;
        --gray-100: #f7fafc;
        --gray-200: #edf2f7;
        --gray-300: #e2e8f0;
    }

    /* Description Section */
    .description-content {
        line-height: 1.8;
        color: #4b5563;
        font-size: 1rem;
    }

    /* Attachment Section */
    .attachment-item {
        background-color: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 10px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .attachment-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: var(--primary);
    }

    .attachment-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .attachment-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--primary-light);
        color: var(--primary);
        border-radius: 8px;
        font-size: 1.25rem;
    }

    .attachment-details h4 {
        font-size: 1rem;
        margin: 0 0 0.25rem;
        color: var(--secondary);
    }

    .attachment-details p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .download-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--primary);
        color: var(--white);
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
    }

    .download-btn:hover {
        background-color: var(--primary-dark);
        transform: scale(1.05);
        color: var(--white);
        text-decoration: none;
    }

    /* Metadata Display */
    .metadata-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1rem;
    }

    .metadata-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .metadata-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-light);
        color: var(--primary);
        font-size: 1rem;
    }

    .metadata-content h4 {
        font-size: 0.8rem;
        color: #6b7280;
        margin: 0 0 0.25rem;
        font-weight: 500;
    }

    .metadata-content p {
        font-size: 0.95rem;
        color: var(--secondary);
        margin: 0;
        font-weight: 500;
    }

    /* Subject Badge */
    .subject-badge {
        display: inline-flex;
        align-items: center;
        background-color: var(--primary-light);
        color: var(--primary);
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.875rem;
        margin-top: 1rem;
        box-shadow: 0 2px 4px rgba(67, 97, 238, 0.1);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .metadata-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
        
        .attachment-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .download-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <a href="{{ route('siswa.materials.index') }}" class="inline-flex items-center text-blue-600 transition-colors hover:text-blue-800">
            <i class="mr-2 text-sm fas fa-chevron-left"></i>
            <span>Kembali ke Daftar Materi</span>
        </a>
    </div>

    <div class="mb-6 overflow-hidden bg-white border shadow-md rounded-xl border-gray-100/50">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-white">
            <div class="flex items-start justify-between">
                <div class="flex items-start">
                    <div class="p-3 mr-4 text-purple-600 bg-purple-100 rounded-full">
                        <i class="text-xl fas fa-book-open"></i>
                    </div>
                    <div>
                        <h1 class="flex items-center text-2xl font-bold text-gray-900">
                            {{ $material->title }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="mr-1 fas fa-calendar-day"></i>
                                {{ $material->created_at->format('d M Y') }}
                            </div>
                            
                            @if($material->teacher && $material->teacher->name)
                            <div class="flex items-center">
                                <i class="mr-1 fas fa-user"></i>
                                {{ $material->teacher->name }}
                            </div>
                            @else
                            <div class="flex items-center">
                                <i class="mr-1 fas fa-user"></i>
                                <span class="text-gray-400">Guru tidak tersedia</span>
                            </div>
                            @endif
                            
                            @if($material->subject)
                            <div class="flex items-center">
                                <i class="mr-1 fas fa-book"></i>
                                {{ $material->subject->name }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="prose max-w-none">
                @if($material->description)
                    {!! nl2br(e($material->description)) !!}
                @else
                    <p class="italic text-gray-500">Tidak ada deskripsi tersedia.</p>
                @endif
            </div>
            
            @if($material->file_path)
            <div class="pt-6 mt-8 border-t border-gray-100">
                <h3 class="mb-3 text-lg font-medium text-gray-900">Lampiran Materi</h3>
                <div class="attachment-item">
                    <div class="attachment-info">
                        <div class="attachment-icon">
                            <i class="fas fa-{{ $material->file_icon }}"></i>
                        </div>
                        <div class="attachment-details">
                            <h4>{{ basename($material->file_path) }}</h4>
                            <p>{{ $material->getFileType() }}</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $material->file_path) }}" class="download-btn" target="_blank">
                        <i class="mr-1 fas fa-download"></i> Unduh
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="p-4 text-sm text-gray-600 border border-gray-100 rounded-lg bg-gray-50">
        <div class="flex items-center">
            <i class="mr-2 text-purple-500 fas fa-info-circle"></i>
            <p>Materi pembelajaran ini dibuat pada {{ $material->created_at->format('d F Y') }} dan terakhir diperbarui pada {{ $material->updated_at->format('d F Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .prose {
        line-height: 1.75;
    }
    
    .prose p {
        margin-top: 1.25em;
        margin-bottom: 1.25em;
    }
    
    .prose strong {
        font-weight: 600;
    }
    
    .prose ul {
        list-style-type: disc;
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        padding-left: 1.625em;
    }
    
    .prose ol {
        list-style-type: decimal;
        margin-top: 1.25em;
        margin-bottom: 1.25em;
        padding-left: 1.625em;
    }
    
    .prose li {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }
</style>
@endpush
