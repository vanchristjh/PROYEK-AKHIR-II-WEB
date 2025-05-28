@extends('layouts.siswa')

@section('title', 'Detail Ujian')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .detail-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(79, 70, 229, 0.08);
    }
    
    .detail-header {
        background: linear-gradient(135deg, #6366F1 0%, #7C3AED 100%);
        color: white;
        padding: 1.5rem;
    }
    
    .countdown-timer {
        font-family: 'Courier New', monospace;
        background-color: rgba(255, 255, 255, 0.15);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: bold;
        color: white;
    }
    
    .rules-list li {
        margin-bottom: 0.75rem;
        position: relative;
        padding-left: 1.75rem;
    }
    
    .rules-list li::before {
        content: "•";
        position: absolute;
        left: 0.5rem;
        color: #6366F1;
        font-size: 1.25rem;
    }
    
    .attempt-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 12px rgba(79, 70, 229, 0.08);
    }
    
    .attempt-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.12);
    }
</style>
@endpush

@section('content')
<div class="container px-4 py-5">    <div class="mb-6">
        <a href="{{ route('siswa.exams.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Ujian
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow detail-card">
                <div class="border-b pb-4 mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
                    <p class="text-gray-600">{{ $exam->description }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Informasi Ujian</h3>
                        <ul class="space-y-1 text-gray-600">
                            <li><span class="font-medium">Mata Pelajaran:</span> {{ $exam->subject->name }}</li>
                            <li><span class="font-medium">Guru:</span> {{ $exam->teacher->name }}</li>
                            <li><span class="font-medium">Jenis Ujian:</span> {{ ucfirst($exam->type) }}</li>
                            <li><span class="font-medium">Jumlah Soal:</span> {{ $exam->questions->count() }}</li>
                            <li><span class="font-medium">Durasi:</span> {{ $exam->duration }} menit</li>
                            <li><span class="font-medium">Nilai Minimum Lulus:</span> {{ $exam->passing_grade }}</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">Waktu Ujian</h3>
                        <ul class="space-y-1 text-gray-600">
                            <li><span class="font-medium">Mulai:</span> {{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y, H:i') }}</li>
                            <li><span class="font-medium">Selesai:</span> {{ \Carbon\Carbon::parse($exam->end_time)->format('d M Y, H:i') }}</li>
                            <li><span class="font-medium">Percobaan Maksimum:</span> {{ $exam->max_attempts }} kali</li>
                        </ul>
                        
                        @php
                            $now = now();
                            $startTime = \Carbon\Carbon::parse($exam->start_time);
                            $endTime = \Carbon\Carbon::parse($exam->end_time);
                            
                            if ($now->isBefore($startTime)) {
                                $remainingTime = $now->diffInSeconds($startTime);
                                $statusText = "Akan Dimulai Dalam";
                            } elseif ($now->isBetween($startTime, $endTime)) {
                                $remainingTime = $now->diffInSeconds($endTime);
                                $statusText = "Waktu Tersisa";
                            } else {
                                $remainingTime = 0;
                                $statusText = "Ujian Telah Berakhir";
                            }
                        @endphp
                        
                        <div class="mt-3">
                            <h4 class="text-sm text-gray-500">{{ $statusText }}</h4>
                            @if($remainingTime > 0)
                                <div class="countdown-timer" id="countdown">00:00:00</div>
                            @else
                                <div class="countdown-timer text-red-600">Waktu Habis</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Petunjuk dan Peraturan</h3>
                    <ul class="rules-list text-gray-600">
                        <li>Kerjakan soal-soal dengan jujur dan mandiri.</li>
                        <li>Pastikan koneksi internet stabil selama mengerjakan ujian.</li>
                        <li>Jangan meninggalkan halaman ujian sampai Anda menyelesaikan semua soal.</li>
                        <li>Jawaban akan otomatis tersimpan setiap kali Anda menjawab pertanyaan.</li>
                        <li>Ujian akan berakhir secara otomatis ketika waktu habis.</li>
                    </ul>
                </div>
                
                <div class="text-center">                    @if(!$exam->is_available)
                        <button disabled class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-500 rounded-md font-medium text-sm tracking-wide cursor-not-allowed">
                            <i class="fas fa-ban mr-2"></i> Ujian Tidak Tersedia Saat Ini
                        </button>
                    @elseif($attemptsCount >= $exam->max_attempts)
                        <button disabled class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-500 rounded-md font-medium text-sm tracking-wide cursor-not-allowed">
                            <i class="fas fa-exclamation-circle mr-2"></i> Batas Percobaan Tercapai
                        </button>
                    @elseif($now->isBefore($startTime))
                        <button disabled class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-500 rounded-md font-medium text-sm tracking-wide cursor-not-allowed">
                            <i class="fas fa-clock mr-2"></i> Ujian Belum Dimulai
                        </button>
                    @elseif($now->isAfter($endTime))
                        <button disabled class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-500 rounded-md font-medium text-sm tracking-wide cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i> Ujian Telah Berakhir
                        </button>
                    @else
                        <form action="{{ route('siswa.exams.start', $exam->id) }}" method="GET">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-md font-medium text-sm text-white tracking-wide hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                <i class="fas fa-play-circle mr-2"></i> Mulai Ujian Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow detail-card">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Percobaan</h2>
                
                @if($attempts->isEmpty())
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500">Anda belum pernah mengerjakan ujian ini.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($attempts as $attempt)
                            <div class="attempt-card p-3 border rounded-md bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Percobaan #{{ $loop->iteration }}</span>
                                        <p class="text-xs text-gray-500">{{ $attempt->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                    <div>
                                        @if($attempt->is_completed)
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                Belum Selesai
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($attempt->is_completed && $attempt->score !== null)
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Nilai:</span>
                                        <span class="font-bold {{ $attempt->score >= $exam->passing_grade ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $attempt->score }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-right">
                                        <a href="{{ route('siswa.exams.result', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}" class="text-xs text-blue-600 hover:underline">
                                            Lihat Hasil
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-sm text-gray-500 text-center">
                        Percobaan yang telah dilakukan: {{ $attemptsCount }}/{{ $exam->max_attempts }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Countdown timer
        const countdownElement = document.getElementById('countdown');
        
        @if($remainingTime > 0)
            let remainingSeconds = {{ $remainingTime }};
            
            function updateCountdown() {
                const hours = Math.floor(remainingSeconds / 3600);
                const minutes = Math.floor((remainingSeconds % 3600) / 60);
                const seconds = remainingSeconds % 60;
                
                countdownElement.textContent = 
                    (hours < 10 ? '0' + hours : hours) + ':' +
                    (minutes < 10 ? '0' + minutes : minutes) + ':' +
                    (seconds < 10 ? '0' + seconds : seconds);
                    
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    countdownElement.textContent = '00:00:00';
                    countdownElement.classList.add('text-red-600');
                    
                    @if($now->isBetween($startTime, $endTime))
                        location.reload();
                    @endif
                }
                
                remainingSeconds--;
            }
            
            updateCountdown();
            const timerInterval = setInterval(updateCountdown, 1000);
        @endif
    });
</script>
@endpush
