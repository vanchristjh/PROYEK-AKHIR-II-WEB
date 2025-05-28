@extends('layouts.app')

@section('styles')
<style>
    .material-header {
        background-color: #4e73df;
        color: white;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .back-btn {
        background-color: white;
        color: #4e73df;
        border: 1px solid #4e73df;
        padding: 5px 15px;
        border-radius: 4px;
    }
    .back-btn:hover {
        background-color: #f8f9fc;
    }
    .material-info-box {
        background-color: #f8f9fc;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }
    .material-metadata {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    .metadata-item {
        margin-right: 20px;
        font-size: 0.9rem;
        color: #666;
    }
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #4e73df;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 10px;
    }
    .description-box {
        background-color: white;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .attachment-list {
        margin-top: 20px;
    }
    .attachment-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        background-color: white;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .attachment-item:hover {
        transform: translateX(5px);
        box-shadow: 0 3px 5px rgba(0,0,0,0.15);
    }
    .attachment-info {
        display: flex;
        align-items: center;
    }
    .attachment-icon {
        font-size: 1.5rem;
        margin-right: 15px;
        color: #4e73df;
    }
    .download-btn {
        background-color: #4e73df;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with title and back button -->
    <div class="material-header">
        <h2>{{ $material->title }}</h2>
        <a href="{{ route('siswa.materials.index') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Materials
        </a>
    </div>
    
    <div class="material-info-box">
        <div class="material-metadata">
            <div class="metadata-item">
                <i class="far fa-calendar-alt mr-1"></i> Posted: {{ $material->created_at->format('d M Y') }}
            </div>
            
            @if($material->created_at != $material->updated_at)
            <div class="metadata-item">
                <i class="far fa-edit mr-1"></i> Updated: {{ $material->updated_at->format('d M Y') }}
            </div>
            @endif
            
            @if($material->teacher)
            <div class="metadata-item">
                <i class="fas fa-chalkboard-teacher mr-1"></i> Teacher: {{ $material->teacher->user->name }}
            </div>
            @endif
        </div>
        
        @if($material->subjects->isNotEmpty())
        <div>
            <strong>Subjects:</strong>
            @foreach($material->subjects as $subject)
                <span class="badge badge-light">{{ $subject->name }}</span>
            @endforeach
        </div>
        @endif
    </div>
    
    <!-- Description section -->
    <div class="section-title">
        <i class="fas fa-align-left mr-2"></i> Description
    </div>
    <div class="description-box">
        {!! $material->description !!}
    </div>
    
    <!-- Attachments section -->
    @if($material->attachments && count($material->attachments) > 0)
    <div class="section-title">
        <i class="fas fa-paperclip mr-2"></i> Attachments
    </div>
    <div class="attachment-list">
        @foreach($material->attachments as $attachment)
        <div class="attachment-item">
            <div class="attachment-info">
                @php
                    $extension = pathinfo($attachment->original_name, PATHINFO_EXTENSION);
                    $icon = 'file-alt';
                    
                    if(in_array($extension, ['pdf'])) $icon = 'file-pdf';
                    elseif(in_array($extension, ['doc', 'docx'])) $icon = 'file-word';
                    elseif(in_array($extension, ['xls', 'xlsx'])) $icon = 'file-excel';
                    elseif(in_array($extension, ['ppt', 'pptx'])) $icon = 'file-powerpoint';
                    elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'file-image';
                    elseif(in_array($extension, ['zip', 'rar'])) $icon = 'file-archive';
                @endphp
                
                <i class="fas fa-{{ $icon }} attachment-icon"></i>
                <div>
                    <div class="font-weight-bold">{{ $attachment->original_name }}</div>
                    <small class="text-muted">{{ number_format(round($attachment->size / 1024, 2), 2) }} KB</small>
                </div>
            </div>
            <a href="{{ asset('storage/' . $attachment->path) }}" class="download-btn" target="_blank">
                <i class="fas fa-download mr-1"></i> Download
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
