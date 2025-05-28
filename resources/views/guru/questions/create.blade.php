@extends('layouts.dashboard')

@section('title', 'Tambah Soal Kuis')

@section('header', 'Tambah Soal Kuis')

@section('content')
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-question-circle text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Tambah Soal Baru</h2>
            <p class="text-purple-100">Tambahkan soal untuk kuis {{ $quiz->title }}.</p>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('guru.quizzes.show', $quiz->id) }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Detail Kuis</span>
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="{{ route('guru.quizzes.questions.store', $quiz->id) }}" method="POST" class="animate-fade-in" id="questionForm">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <div class="form-group mb-5">
                        <label for="question_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Soal <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-list-ul text-gray-400"></i>
                            </div>
                            <select name="question_type" id="question_type" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Tipe Soal</option>
                                <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                                <option value="essay" {{ old('question_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                            </select>
                        </div>
                        @error('question_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group mb-5">
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Poin Soal <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-star text-gray-400"></i>
                            </div>
                            <input type="number" name="points" id="points" min="0" step="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" 
                                value="{{ old('points', 1) }}" required>
                        </div>
                        @error('points')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="content" id="content" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>{{ old('content') }}</textarea>
                        </div>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="multiple_choice_options" class="form-group mb-5 {{ old('question_type') == 'multiple_choice' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center justify-between">
                            <span>Pilihan Jawaban <span class="text-red-500">*</span></span>
                            <button type="button" id="add_option" class="text-xs bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded">
                                <i class="fas fa-plus mr-1"></i> Tambah Pilihan
                            </button>
                        </label>
                        
                        <div id="options_container" class="space-y-3">
                            <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <input type="radio" name="options[0][is_correct]" value="1" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                </div>
                                <div class="ml-3 flex-1">
                                    <input type="text" name="options[0][content]" placeholder="Masukkan opsi jawaban" 
                                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                </div>
                            </div>
                            <div class="option-row relative bg-gray-50 rounded-lg p-4 flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <input type="radio" name="options[1][is_correct]" value="1" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                                </div>
                                <div class="ml-3 flex-1">
                                    <input type="text" name="options[1][content]" placeholder="Masukkan opsi jawaban"
                                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- True/False Options -->
                    <div id="true_false_options" class="form-group mb-5 {{ old('question_type') == 'true_false' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Jawaban Benar <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="options[0][is_correct]" value="1" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <input type="hidden" name="options[0][content]" value="Benar">
                                <label class="ml-2 text-gray-700">Benar</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="options[1][is_correct]" value="1" class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                                <input type="hidden" name="options[1][content]" value="Salah">
                                <label class="ml-2 text-gray-700">Salah</label>
                            </div>
                        </div>
                    </div>

                    <!-- Essay Answer -->
                    <div id="essay_answer" class="form-group mb-5 {{ old('question_type') == 'essay' ? '' : 'hidden' }}">
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-1">Kunci Jawaban <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <textarea name="correct_answer" id="correct_answer" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">{{ old('correct_answer') }}</textarea>
                            <p class="text-gray-500 text-xs mt-1">Kunci jawaban ini akan digunakan sebagai panduan untuk penilaian.</p>
                        </div>
                        @error('correct_answer')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('guru.quizzes.show', $quiz->id) }}" 
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            Simpan Soal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionTypeSelect = document.getElementById('question_type');
        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
        const trueFalseOptions = document.getElementById('true_false_options');
        const essayAnswer = document.getElementById('essay_answer');
        let optionCounter = 2; // Starting from 2 since we already have 2 default options

        // Question type change handler
        questionTypeSelect.addEventListener('change', function() {
            multipleChoiceOptions.classList.add('hidden');
            trueFalseOptions.classList.add('hidden');
            essayAnswer.classList.add('hidden');

            switch(this.value) {
                case 'multiple_choice':
                    multipleChoiceOptions.classList.remove('hidden');
                    break;
                case 'true_false':
                    trueFalseOptions.classList.remove('hidden');
                    break;
                case 'essay':
                    essayAnswer.classList.remove('hidden');
                    break;
            }
        });

        // Add option button click handler
        document.getElementById('add_option').addEventListener('click', function() {
            const optionsContainer = document.getElementById('options_container');
            const newOption = document.createElement('div');
            newOption.className = 'option-row relative bg-gray-50 rounded-lg p-4 flex items-start';
            newOption.innerHTML = `
                <div class="flex-shrink-0 mt-1">
                    <input type="radio" name="options[${optionCounter}][is_correct]" value="1" 
                        class="rounded-full border-gray-300 text-purple-600 focus:ring-purple-500">
                </div>
                <div class="ml-3 flex-1">
                    <input type="text" name="options[${optionCounter}][content]" placeholder="Masukkan opsi jawaban"
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                </div>
                <button type="button" class="remove-option ml-2 text-red-500 hover:text-red-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            `;
            optionsContainer.appendChild(newOption);
            optionCounter++;
        });

        // Remove option button click handler (delegated)
        document.getElementById('options_container').addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionRow = e.target.closest('.option-row');
                if (document.querySelectorAll('.option-row').length > 2) {
                    optionRow.remove();
                } else {
                    alert('Minimal harus ada 2 pilihan jawaban.');
                }
            }
        });
    });
</script>
@endpush
