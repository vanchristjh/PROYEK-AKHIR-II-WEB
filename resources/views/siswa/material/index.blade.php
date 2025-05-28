@extends('layouts.app')

@section('styles')
<style>
    .materials-header {
        background-color: #4e73df;
        color: white;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .materials-header h2 {
        margin: 0;
        font-size: 1.5rem;
    }
    .search-box {
        margin-bottom: 20px;
        width: 100%;
        max-width: 400px;
    }
    .material-item {
        display: flex;
        margin-bottom: 15px;
        padding: 15px;
        border-radius: 4px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .material-item:hover {
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }
    .material-icon {
        width: 40px;
        height: 40px;
        margin-right: 15px;
        color: #4e73df;
    }
    .material-title {
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 5px;
    }
    .material-meta {
        color: #6c757d;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }
    .material-description {
        margin-top: 5px;
        font-size: 0.9rem;
    }
    .filter-btn {
        padding: 5px 15px;
        border-radius: 4px;
        background-color: white;
        color: #4e73df;
        border: 1px solid #4e73df;
    }
    .filter-btn:hover {
        background-color: #f8f9fc;
    }
    .empty-state {
        text-align: center;
        padding: 40px 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header section with title and filter -->
    <div class="materials-header">
        <h2><i class="fas fa-book-open mr-2"></i> Learning Materials</h2>
        <button class="filter-btn">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
    </div>

    <!-- Search box -->
    <div class="search-box">
        <input type="text" class="form-control" id="searchMaterials" placeholder="Search materials...">
    </div>

    @if($materials->isEmpty())
    <div class="empty-state">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <h4>No Learning Materials Yet</h4>
        <p class="text-muted">Check back later for new materials from your teachers</p>
    </div>
    @else
    <!-- Materials list view -->
    <div class="materials-list" id="materialsContainer">
        @foreach($materials as $material)
        <div class="material-item">
            <div class="material-icon">
                @php 
                $icon = 'file-alt';
                $fileType = $material->file_type ?? '';
                
                if(Str::contains($fileType, 'pdf')) $icon = 'file-pdf';
                elseif(Str::contains($fileType, ['ppt', 'pptx'])) $icon = 'file-powerpoint';
                elseif(Str::contains($fileType, ['doc', 'docx'])) $icon = 'file-word';
                elseif(Str::contains($fileType, ['xls', 'xlsx'])) $icon = 'file-excel';
                @endphp
                <i class="fas fa-{{ $icon }} fa-2x"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="material-title">{{ $material->title }}</h5>
                <div class="material-meta">
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ $material->created_at->format('d M Y') }}</span>
                    @if($material->subjects->isNotEmpty())
                    <span class="ml-3">
                        <i class="fas fa-book mr-1"></i> 
                        {{ $material->subjects->first()->name }}
                        @if($material->subjects->count() > 1)
                        <span class="badge badge-light ml-1">+{{ $material->subjects->count() - 1 }}</span>
                        @endif
                    </span>
                    @endif
                </div>
                <p class="material-description">{{ Str::limit($material->description ?? 'No description available', 150) }}</p>
                <div>
                    <a href="{{ route('siswa.material.show', $material->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $materials->links() }}
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Filter materials when typing in search box
        $('#searchMaterials').on('keyup', function() {
            let searchText = $(this).val().toLowerCase();
            $('.material-item').each(function() {
                let itemText = $(this).text().toLowerCase();
                if(itemText.indexOf(searchText) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection
