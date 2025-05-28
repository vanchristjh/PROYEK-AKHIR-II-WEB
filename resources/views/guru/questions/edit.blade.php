@extends('layouts.dashboard')

@section('title', 'Edit Soal Kuis')

@section('header', 'Edit Soal Kuis')

@section('content')
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-edit text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Edit Soal</h2>
            <p class="text-purple-100">Perbarui soal untuk kuis {{ $question->quiz->title }}</p>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('guru.quizzes.show', $question->quiz_id) }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Detail Kuis</span>
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="{{ route('guru.questions.update', $question->id) }}" method="POST" class="animate-fade-in" id="questionForm">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="form-group mb-5">
                        <label for="question_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-list-ul text-gray-400"></i>
                            </div>
                            <select name="type" id="question_type" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Tipe Soal</option>
                                <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="true_false" {{ old('type', $question->type) == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                                <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>Essay</option>
                            </select>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="content" id="content" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>{{ old('content', $question->content) }}</textarea>
                        </div>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="multiple_choice_options" class="form-group mb-5 {{ old('type', $question->type) == 'multiple_choice' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center justify-between">
                            <span>Pilihan Jawaban <span class="text-red-500">*</span></span>
                            <button type="button" id="add_option" class="text-xs bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded">
                                <i class="fas fa-plus mr-1"></i> Tambah Pilihan
                            </button>
                        </label>
                        
                        <div id="options_container" class="space-y-3">
                            @if(old('options'))
                                @foreach(old('options') as $index => $option)
                                    <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                        <div class="flex-shrink-0 mt-1">
                                            <input type="radio" name="correct_option" value="{{ $index }}" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500" 
                                                {{ old('correct_option') == $index ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <input type="text" name="options[]" placeholder="Masukkan opsi jawaban" value="{{ $option }}"
                                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                        <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @elseif($question->type == 'multiple_choice' && $question->options)
                                @foreach($question->options as $index => $option)
                                    <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                        <div class="flex-shrink-0 mt-1">
                                            <input type="radio" name="correct_option" value="{{ $index }}" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500" 
                                                {{ $option['is_correct'] ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <input type="text" name="options[]" placeholder="Masukkan opsi jawaban" value="{{ $option['text'] }}"
                                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                        </div>
                                        <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="radio" name="correct_option" value="0" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <input type="text" name="options[]" placeholder="Masukkan opsi jawaban"
                                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    </div>
                                    <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <input type="radio" name="correct_option" value="1" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <input type="text" name="options[]" placeholder="Masukkan opsi jawaban"
                                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    </div>
                                    <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        @error('options')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @error('correct_option')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- True/False Option -->
                    <div id="true_false_option" class="form-group mb-5 {{ old('type', $question->type) == 'true_false' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jawaban Benar <span class="text-red-500">*</span></label>
                        <div class="mt-1 bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input id="true_option" name="answer" type="radio" value="true" 
                                        {{ old('answer', $question->answer) == 'true' ? 'checked' : '' }}
                                        class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                                    <label for="true_option" class="ml-2 text-sm text-gray-700">Benar</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="false_option" name="answer" type="radio" value="false" 
                                        {{ old('answer', $question->answer) == 'false' ? 'checked' : '' }}
                                        class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                                    <label for="false_option" class="ml-2 text-sm text-gray-700">Salah</label>
                                </div>
                            </div>
                        </div>
                        @error('answer')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Essay Answer -->
                    <div id="essay_answer" class="form-group mb-5 {{ old('type', $question->type) == 'essay' ? '' : 'hidden' }}">
                        <label for="answer_key" class="block text-sm font-medium text-gray-700 mb-1">Kunci Jawaban (Untuk Panduan Penilaian)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="answer_key" id="answer_key" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300">{{ old('answer_key', $question->answer_key) }}</textarea>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Jawaban akhir akan dinilai secara manual oleh guru.</p>
                        @error('answer_key')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group mb-5">
                        <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Bobot Nilai <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-star text-gray-400"></i>
                            </div>
                            <input type="number" name="score" id="score" value="{{ old('score', $question->score) }}" min="1" max="100" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Nilai untuk soal ini (1-100)</p>
                        @error('score')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('guru.quizzes.show', $question->quiz_id) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg hover:from-purple-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Perbarui Soal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-fade-in {
        animation: fade-in 0.6s ease-in-out;
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
    
    .animate-fade-out {
        animation: fade-out 0.3s ease-in-out forwards;
    }
    
    @keyframes fade-out {
        0% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 0;
            transform: translateY(10px);
        }
    }
    
    .form-group:focus-within label {
        color: #8B5CF6;
    }
    
    .form-group:focus-within i {
        color: #8B5CF6;
    }
    
    .option-row {
        transition: all 0.3s ease;
    }
    
    .option-row:hover {
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionType = document.getElementById('question_type');
        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
        const trueFalseOption = document.getElementById('true_false_option');
        const essayAnswer = document.getElementById('essay_answer');
        
        // Show/hide options based on question type
        questionType.addEventListener('change', function() {
            const selectedType = this.value;
            
            multipleChoiceOptions.classList.toggle('hidden', selectedType !== 'multiple_choice');
            trueFalseOption.classList.toggle('hidden', selectedType !== 'true_false');
            essayAnswer.classList.toggle('hidden', selectedType !== 'essay');
        });
        
        // Add option button
        const addOptionBtn = document.getElementById('add_option');
        const optionsContainer = document.getElementById('options_container');
        
        if (addOptionBtn && optionsContainer) {
            addOptionBtn.addEventListener('click', function() {
                const optionCount = optionsContainer.querySelectorAll('.option-row').length;
                
                const optionDiv = document.createElement('div');
                optionDiv.className = 'option-row relative bg-gray-50 rounded-lg p-4 flex items-start';
                optionDiv.innerHTML = `
                    <div class="flex-shrink-0 mt-1">
                        <input type="radio" name="correct_option" value="${optionCount}" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                    </div>
                    <div class="ml-3 flex-1">
                        <input type="text" name="options[]" placeholder="Masukkan opsi jawaban"
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    </div>
                    <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                optionsContainer.appendChild(optionDiv);
                
                // Add event listener to remove button
                optionDiv.querySelector('.remove-option').addEventListener('click', function() {
                    removeOption(this);
                });
                
                // Animation
                optionDiv.classList.add('animate-fade-in');
                setTimeout(() => {
                    optionDiv.classList.remove('animate-fade-in');
                }, 500);
                
                updateOptionsIndexes();
            });
            
            // Add event listeners to existing remove buttons
            document.querySelectorAll('.remove-option').forEach(button => {
                button.addEventListener('click', function() {
                    removeOption(this);
                });
            });
            
            // Remove option function
            function removeOption(button) {
                const optionRow = button.closest('.option-row');
                const totalOptions = optionsContainer.querySelectorAll('.option-row').length;
                
                if (totalOptions > 2) {
                    optionRow.classList.add('animate-fade-out');
                    setTimeout(() => {
                        optionRow.remove();
                        updateOptionsIndexes();
                    }, 300);
                } else {
                    alert('Minimal harus ada 2 pilihan jawaban');
                }
            }
            
            // Update correct_option radios after removing an option
            function updateOptionsIndexes() {
                const optionRows = optionsContainer.querySelectorAll('.option-row');
                optionRows.forEach((row, index) => {
                    row.querySelector('input[name="correct_option"]').value = index;
                });
            }
        }
        
        // Form validation
        const form = document.getElementById('questionForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                const selectedType = questionType.value;
                let hasError = false;
                
                // Validate content
                const content = document.getElementById('content');
                if (!content.value.trim()) {
                    alert('Pertanyaan tidak boleh kosong');
                    hasError = true;
                }
                
                // Validate multiple choice options
                if (selectedType === 'multiple_choice') {
                    const options = document.querySelectorAll('input[name="options[]"]');
                    const correctOption = document.querySelector('input[name="correct_option"]:checked');
                    
                    if (!correctOption) {
                        alert('Pilih salah satu opsi sebagai jawaban yang benar');
                        hasError = true;
                    }
                    
                    options.forEach((option, index) => {
                        if (!option.value.trim()) {
                            alert(`Opsi jawaban #${index + 1} tidak boleh kosong`);
                            hasError = true;
                        }
                    });
                }
                
                // Validate true/false
                if (selectedType === 'true_false') {
                    const answer = document.querySelector('input[name="answer"]:checked');
                    if (!answer) {
                        alert('Pilih jawaban benar atau salah');
                        hasError = true;
                    }
                }
                
                if (hasError) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
@endpush
