@extends('layouts.dashboard')

@section('title', 'Materi Pembelajaran')

@section('header', 'Materi Pembelajaran')

@section('styles')
<style>
    .animate-fade-in {
        animation: fade-in 0.5s ease-in-out;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) translateX(0); }
        25% { transform: translateY(-10px) translateX(5px); }
        50% { transform: translateY(-5px) translateX(10px); }
        75% { transform: translateY(10px) translateX(-5px); }
    }
    
    /* Line clamp for description text */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .material-card {
        transition: all 0.3s ease;
    }
    
    .material-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .file-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .empty-state {
        padding: 60px 0;
    }
    
    /* Particle Animation */
    .particles-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    
    .particle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        animation: float 6s infinite ease-in-out;
    }
</style>
@endsection

@section('content')
    <!-- Header with animated gradient -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-xl p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-book-open text-9xl"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-1/3 -top-12 w-36 h-36 bg-indigo-300/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Materi Pembelajaran</h2>
            <p class="text-purple-100">Akses semua materi pembelajaran dari guru Anda di sini.</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative flex-grow max-w-lg">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchMaterials" class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="Cari materi...">
            </div>
            <div class="flex items-center space-x-2">
                <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-filter mr-2 text-purple-500"></i> 
                    <span>Filter</span>
                </button>
                <button class="flex items-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    <i class="fas fa-sort-amount-down-alt mr-2"></i> 
                    <span>Sort</span>
                </button>
            </div>
        </div>
    </div>

    @if($materials->isEmpty())
    <!-- Empty state with enhanced styling -->
    <div class="bg-white rounded-xl shadow-sm p-10 empty-state text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 text-purple-500 mb-4">
            <i class="fas fa-folder-open text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Materi Pembelajaran</h3>
        <p class="text-gray-500 max-w-md mx-auto">Materi pembelajaran akan ditampilkan di sini saat guru Anda membagikannya.</p>
    </div>
    @else
    <!-- Materials grid with enhanced card design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="materialsContainer">
        @foreach($materials as $material)
        <div class="material-card bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/60">
            @php 
            $icon = 'file-alt';
            $fileType = $material->file_type ?? '';
            $fileColor = 'text-blue-500';
            
            if(Str::contains($fileType, 'pdf')) {
                $icon = 'file-pdf';
                $fileColor = 'text-red-500';
            }
            elseif(Str::contains($fileType, ['ppt', 'pptx'])) {
                $icon = 'file-powerpoint';
                $fileColor = 'text-orange-500'; 
            }
            elseif(Str::contains($fileType, ['doc', 'docx'])) {
                $icon = 'file-word';
                $fileColor = 'text-blue-600';
            }
            elseif(Str::contains($fileType, ['xls', 'xlsx'])) {
                $icon = 'file-excel';
                $fileColor = 'text-green-600';
            }
            @endphp
            
            <div class="p-5">
                <div class="flex items-start space-x-3">
                    <div class="file-icon rounded-lg bg-gray-100/80 flex-shrink-0">
                        <i class="fas fa-{{ $icon }} text-xl {{ $fileColor }}"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $material->title }}</h3>
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <span class="flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i> 
                                {{ $material->created_at->format('d M Y') }}
                            </span>
                            @if($material->subject)
                            <span class="mx-2">•</span>
                            <span>
                                <i class="fas fa-book mr-1"></i> 
                                {{ $material->subject->name }}
                            </span>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $material->description ?? 'Tidak ada deskripsi tersedia' }}</p>
                        <a href="{{ route('siswa.materials.show', $material->id) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                            <i class="fas fa-eye mr-1.5"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination with enhanced styling -->
    <div class="mt-6">
        <div class="pagination-wrapper bg-white rounded-lg shadow-sm p-3">
            {{ $materials->links() }}
        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter materials when typing in search box
        document.getElementById('searchMaterials').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            document.querySelectorAll('.material-card').forEach(function(card) {
                let itemText = card.textContent.toLowerCase();
                if(itemText.indexOf(searchText) > -1) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Create floating particle effects for headers
        function createParticles() {
            const container = document.querySelector('.particles-container');
            if (!container) return;
            
            // Clear existing particles
            container.innerHTML = '';
            
            // Create new particles
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size between 5px and 20px
                const size = Math.floor(Math.random() * 15) + 5;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random delay for staggered animation
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                container.appendChild(particle);
            }
        }
        
        createParticles();

        // Filter materials with jQuery (from second script section)
        $(document).ready(function() {
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
    });
</script>
@endsection
