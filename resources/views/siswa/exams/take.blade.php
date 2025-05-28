@extends('layouts.exam')

@section('title', 'Mengerjakan Ujian: ' . $exam->title)

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        background-color: #f3f4f6;
    }
    
    .question-container {
        transition: opacity 0.3s ease;
    }
    
    .question-navigation .active {
        background-color: #3B82F6;
        color: white;
    }
    
    .question-navigation button:hover:not(.active) {
        background-color: #E5E7EB;
    }
    
    .option-label {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .option-label:hover {
        background-color: #EFF6FF;
    }
    
    .selected-option {
        background-color: #DBEAFE;
        border-color: #3B82F6;
    }
    
    .countdown-timer {
        font-family: 'Courier New', monospace;
        background-color: #f3f4f6;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: bold;
    }
    
    .warning-timer {
        background-color: #FECACA;
        color: #B91C1C;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    
    .confirmation-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
    }
    
    .confirmation-modal-content {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        max-width: 28rem;
        width: 100%;
        padding: 1.5rem;
    }
    
    .editor-container {
        border: 1px solid #E5E7EB;
        border-radius: 0.375rem;
        min-height: 200px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-lg shadow">
        <h1 class="text-xl font-bold text-gray-900">{{ $exam->title }}</h1>
        
        <div class="flex items-center space-x-4">
            <div>
                <h4 class="text-sm text-gray-500">Waktu Tersisa</h4>
                <div class="countdown-timer" id="countdown">00:00:00</div>
            </div>
            
            <button id="submit-exam-btn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white tracking-widest hover:bg-green-700 transition ease-in-out duration-150">
                Kumpulkan Ujian
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Left sidebar for navigation -->
        <div class="md:col-span-1">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="font-semibold text-gray-800 mb-3">Navigasi Soal</h2>
                
                <div class="question-navigation grid grid-cols-5 gap-2">
                    @foreach($exam->questions as $index => $question)
                        <button 
                            type="button" 
                            class="question-nav-btn h-10 w-full flex items-center justify-center rounded-md text-sm font-medium {{ $index === 0 ? 'active' : '' }}" 
                            data-question="{{ $index + 1 }}"
                            data-answered="false"
                        >
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                        <span class="text-sm text-gray-600">Sedang Dikerjakan</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="w-4 h-4 rounded-full bg-green-500"></div>
                        <span class="text-sm text-gray-600">Sudah Dijawab</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="w-4 h-4 rounded-full bg-gray-300"></div>
                        <span class="text-sm text-gray-600">Belum Dijawab</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t">
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Mata Pelajaran:</span> {{ $exam->subject->name }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Jumlah Soal:</span> {{ $exam->questions->count() }}
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Nilai Minimum:</span> {{ $exam->passing_grade }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="md:col-span-3">
            <div class="bg-white p-6 rounded-lg shadow">
                <form id="exam-form" method="POST" action="{{ route('siswa.exams.submit', ['exam' => $exam->id, 'attempt' => isset($attempt) ? $attempt->id : $ongoingAttempt->id]) }}">
                    @csrf
                    
                    @foreach($exam->questions as $index => $question)
                        <div class="question-container {{ $index > 0 ? 'hidden' : '' }}" id="question-{{ $index + 1 }}">
                            <div class="mb-1 text-sm text-blue-600">Soal {{ $index + 1 }} dari {{ $exam->questions->count() }}</div>
                            <h3 class="text-lg font-semibold mb-3">{{ $question->question_text }}</h3>
                            
                            @if($question->image_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question image" class="max-w-full h-auto rounded-lg">
                                </div>
                            @endif
                            
                            <div class="mt-4">
                                @if($question->question_type == 'multiple_choice')
                                    <div class="space-y-2">
                                        @foreach($question->options as $option)
                                            <label class="option-label flex items-start p-3 border rounded-md" data-option-id="{{ $option->id }}">
                                                <input type="radio" name="answers[{{ $question->id }}][option_id]" value="{{ $option->id }}" class="mt-1 mr-3 option-input">
                                                <div>
                                                    <span class="font-medium">{{ chr(64 + $loop->iteration) }}.</span>
                                                    <span>{{ $option->option_text }}</span>
                                                    
                                                    @if($option->image_path)
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $option->image_path) }}" alt="Option image" class="max-w-full h-auto rounded-lg">
                                                        </div>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="answers[{{ $question->id }}][answer_type]" value="option">
                                @elseif($question->question_type == 'true_false')
                                    <div class="space-y-2">
                                        @foreach($question->options as $option)
                                            <label class="option-label flex items-center p-3 border rounded-md" data-option-id="{{ $option->id }}">
                                                <input type="radio" name="answers[{{ $question->id }}][option_id]" value="{{ $option->id }}" class="mr-3 option-input">
                                                <span>{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="answers[{{ $question->id }}][answer_type]" value="option">
                                @elseif($question->question_type == 'essay')
                                    <div>
                                        <textarea 
                                            name="answers[{{ $question->id }}][text_answer]" 
                                            class="w-full h-48 p-3 border rounded-md essay-answer" 
                                            placeholder="Tulis jawaban Anda di sini..."
                                        ></textarea>
                                    </div>
                                    <input type="hidden" name="answers[{{ $question->id }}][answer_type]" value="text">
                                @endif
                            </div>
                            
                            <div class="mt-6 flex justify-between">
                                <button 
                                    type="button" 
                                    class="prev-question inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300 {{ $index === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $index === 0 ? 'disabled' : '' }}
                                >
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Soal Sebelumnya
                                </button>
                                
                                <button 
                                    type="button" 
                                    class="next-question inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 {{ $index === $exam->questions->count() - 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $index === $exam->questions->count() - 1 ? 'disabled' : '' }}
                                >
                                    Soal Berikutnya
                                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submit-modal" class="confirmation-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div class="confirmation-modal-content">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Konfirmasi Pengumpulan</h3>
            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin mengumpulkan ujian ini? Pastikan semua pertanyaan telah dijawab.</p>
            <div class="flex justify-end space-x-3">
                <button id="cancel-submit" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 font-medium">Kembali</button>
                <button id="confirm-submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">Ya, Kumpulkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const attemptId = {{ isset($attempt) ? $attempt->id : $ongoingAttempt->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Handles showing and hiding questions
        const questions = document.querySelectorAll('.question-container');
        const questionNavBtns = document.querySelectorAll('.question-nav-btn');
        let currentQuestion = 1;
        
        function showQuestion(questionNumber) {
            questions.forEach((question, index) => {
                if (index + 1 === questionNumber) {
                    question.classList.remove('hidden');
                } else {
                    question.classList.add('hidden');
                }
            });
            
            questionNavBtns.forEach((btn, index) => {
                if (index + 1 === questionNumber) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            
            currentQuestion = questionNumber;
        }
        
        // Initialize navigation buttons
        questionNavBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const questionNumber = parseInt(this.getAttribute('data-question'));
                showQuestion(questionNumber);
            });
        });
        
        // Next and previous buttons
        document.querySelectorAll('.next-question').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                if (index < questions.length - 1) {
                    showQuestion(currentQuestion + 1);
                }
            });
        });
        
        document.querySelectorAll('.prev-question').forEach((btn, index) => {
            btn.addEventListener('click', function() {
                if (currentQuestion > 1) {
                    showQuestion(currentQuestion - 1);
                }
            });
        });
        
        // Handle selected options
        document.querySelectorAll('.option-label').forEach(label => {
            label.addEventListener('click', function() {
                const questionId = this.closest('.question-container').id.replace('question-', '');
                const optionId = this.getAttribute('data-option-id');
                const input = this.querySelector('.option-input');
                
                // Add selected class to the clicked label and remove from siblings
                this.closest('.space-y-2').querySelectorAll('.option-label').forEach(l => {
                    l.classList.remove('selected-option');
                });
                this.classList.add('selected-option');
                
                // Update navigation button to show answered
                document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.backgroundColor = '#10B981';
                document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.color = 'white';
                document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).setAttribute('data-answered', 'true');
                
                // Save the answer to the server
                saveAnswer(questionId, 'option', optionId);
            });
        });
        
        // Handle essay answers
        document.querySelectorAll('.essay-answer').forEach(textarea => {
            textarea.addEventListener('input', debounce(function() {
                const questionId = this.closest('.question-container').id.replace('question-', '');
                const answer = this.value.trim();
                
                // Update navigation button to show answered (if text is not empty)
                if (answer) {
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.backgroundColor = '#10B981';
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.color = 'white';
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).setAttribute('data-answered', 'true');
                } else {
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.backgroundColor = '';
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).style.color = '';
                    document.querySelector(`.question-nav-btn[data-question="${questionId}"]`).setAttribute('data-answered', 'false');
                }
                
                // Save the answer to the server
                saveAnswer(questionId, 'text', null, answer);
            }, 1000));
        });
        
        // Function to save answers to server
        function saveAnswer(questionNumber, answerType, optionId = null, textAnswer = null) {
            const questionIndex = parseInt(questionNumber) - 1;
            const question = questions[questionIndex];
            const questionId = {{ $exam->id }};
            
            fetch(`/siswa/exams/${questionId}/attempts/${attemptId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    question_id: question.querySelector('input[type="hidden"]').value,
                    answer_type: answerType,
                    option_id: optionId,
                    text_answer: textAnswer
                })
            }).then(response => {
                if (!response.ok) {
                    console.error('Error saving answer');
                }
                return response.json();
            }).catch(error => {
                console.error('Network error:', error);
            });
        }
        
        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Countdown timer
        const countdownElement = document.getElementById('countdown');
        const submitBtn = document.getElementById('submit-exam-btn');
        const submitModal = document.getElementById('submit-modal');
        const confirmSubmit = document.getElementById('confirm-submit');
        const cancelSubmit = document.getElementById('cancel-submit');
        
        let timeLeft = {{ $timeLeft ?? ($exam->duration * 60) }};
        
        function updateCountdown() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = 
                (hours < 10 ? '0' + hours : hours) + ':' +
                (minutes < 10 ? '0' + minutes : minutes) + ':' +
                (seconds < 10 ? '0' + seconds : seconds);
                
            if (timeLeft <= 300) { // 5 minutes warning
                countdownElement.classList.add('warning-timer');
            }
                
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('exam-form').submit();
            }
            
            timeLeft--;
        }
        
        updateCountdown();
        const timerInterval = setInterval(updateCountdown, 1000);
        
        // Submit confirmation
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            submitModal.style.display = 'block';
        });
        
        cancelSubmit.addEventListener('click', function() {
            submitModal.style.display = 'none';
        });
        
        confirmSubmit.addEventListener('click', function() {
            document.getElementById('exam-form').submit();
        });
    });
</script>
@endpush
