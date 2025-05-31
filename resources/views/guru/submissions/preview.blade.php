@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Preview Submission</h5>
                        <small>Assignment: {{ $assignment->title }}</small>
                    </div>
                    <a href="{{ route('guru.assignments.submissions.show', [$assignment->id, $submission->id]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h6>Student Information</h6>
                        <p>Name: {{ $submission->user->name }}<br>
                        Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <h6>Submission Details</h6>
                        <p>{{ $submission->notes ?? 'No notes provided.' }}</p>
                    </div>

                    @if($files && $files->count() > 0)
                        <div class="mb-4">
                            <h6>Submission Files</h6>
                            <div class="list-group">
                                @foreach($files as $file)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <h6 class="mb-1">{{ $file->original_name ?? 'Unnamed file' }}</h6>
                                            <div>
                                                {{-- Use the existing download route instead of the missing one --}}
                                                @if(isset($file->file_path))
                                                    <a href="{{ route('guru.assignments.submissions.download', [$assignment->id, $submission->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                @else
                                                    {{-- If you need to handle multiple files in the future, you might need to add a new route --}}
                                                    <a href="{{ route('guru.assignments.submissions.download', [$assignment->id, $submission->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <small>Size: {{ isset($file->size) ? number_format($file->size / 1024, 2) : 'Unknown' }} KB</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No files were submitted.
                        </div>
                    @endif
                    
                    @if($submission->score)
                        <div class="mb-4">
                            <h6>Assessment</h6>
                            <div class="alert alert-{{ $submission->score >= 60 ? 'success' : 'warning' }}">
                                <strong>Score:</strong> {{ $submission->score }}/100<br>
                                <strong>Feedback:</strong> {{ $submission->feedback ?? 'No feedback provided.' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
