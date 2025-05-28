@props(['question', 'number', 'showAnswer' => false, 'userAnswer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm p-5 border border-gray-100/60 hover:shadow-md transition-shadow']) }}>
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <div class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-800 text-xs font-medium">
            Soal #{{ $number }}
        </div>
        <div class="px-2.5 py-1 rounded-md bg-{{ $question->type == 'multiple_choice' ? 'blue' : ($question->type == 'true_false' ? 'green' : 'amber') }}-100 text-{{ $question->type == 'multiple_choice' ? 'blue' : ($question->type == 'true_false' ? 'green' : 'amber') }}-800 text-xs font-medium">
            {{ $question->type == 'multiple_choice' ? 'Pilihan Ganda' : ($question->type == 'true_false' ? 'Benar/Salah' : 'Essay') }}
        </div>
        @if($showAnswer && $userAnswer)
            <div class="px-2.5 py-1 rounded-md text-xs font-medium {{ $userAnswer->is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $userAnswer->is_correct ? 'Benar' : 'Salah' }}
            </div>
        @endif
        <div class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 text-xs font-medium">
            {{ $question->score ?? 1 }} Poin
        </div>
    </div>

    <div class="prose max-w-none mb-4">
        <h4 class="text-base font-medium text-gray-800">{{ $question->content }}</h4>
    </div>

    @if($question->type == 'multiple_choice')
        <div class="mt-3 pl-4 space-y-2">
            @foreach($question->options as $i => $option)
                <div class="flex items-center {{ $showAnswer ? ($option['is_correct'] ? 'text-green-700' : ($userAnswer && $userAnswer->answer == $i && !$option['is_correct'] ? 'text-red-700' : 'text-gray-700')) : 'text-gray-700' }}">
                    @if(!$showAnswer)
                        <input type="radio" name="answer_{{ $question->id }}" value="{{ $i }}" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-2" {{ $userAnswer && $userAnswer->answer == $i ? 'checked' : '' }} {{ $userAnswer ? 'disabled' : '' }}>
                    @else
                        <div class="w-5 h-5 rounded-full 
                            {{ $option['is_correct'] ? 'bg-green-500 text-white' : 
                              ($userAnswer && $userAnswer->answer == $i && !$option['is_correct'] ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                            flex items-center justify-center text-xs mr-2">
                            {{ $option['is_correct'] ? '✓' : ($userAnswer && $userAnswer->answer == $i && !$option['is_correct'] ? '✗' : '') }}
                        </div>
                    @endif
                    <span class="text-sm {{ $showAnswer && ($userAnswer && $userAnswer->answer == $i || $option['is_correct']) ? 'font-medium' : '' }}">
                        {{ $option['text'] }}
                        @if($showAnswer && $userAnswer && $userAnswer->answer == $i)
                            <span class="text-xs ml-2">(Jawaban Anda)</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    @elseif($question->type == 'true_false')
        <div class="mt-3 pl-4">
            <div class="flex space-x-6">
                @if(!$showAnswer)
                    <div class="flex items-center">
                        <input type="radio" id="true_{{ $question->id }}" name="answer_{{ $question->id }}" value="true" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-2" {{ $userAnswer && $userAnswer->answer == 'true' ? 'checked' : '' }} {{ $userAnswer ? 'disabled' : '' }}>
                        <label for="true_{{ $question->id }}" class="text-sm text-gray-700">Benar</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="false_{{ $question->id }}" name="answer_{{ $question->id }}" value="false" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 mr-2" {{ $userAnswer && $userAnswer->answer == 'false' ? 'checked' : '' }} {{ $userAnswer ? 'disabled' : '' }}>
                        <label for="false_{{ $question->id }}" class="text-sm text-gray-700">Salah</label>
                    </div>
                @else
                    <div class="flex items-center {{ $question->answer == 'true' ? 'text-green-700' : ($userAnswer && $userAnswer->answer == 'true' && $question->answer != 'true' ? 'text-red-700' : 'text-gray-700') }}">
                        <div class="w-5 h-5 rounded-full 
                            {{ $question->answer == 'true' ? 'bg-green-500 text-white' : 
                              ($userAnswer && $userAnswer->answer == 'true' && $question->answer != 'true' ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                            flex items-center justify-center text-xs mr-2">
                            {{ $question->answer == 'true' ? '✓' : ($userAnswer && $userAnswer->answer == 'true' && $question->answer != 'true' ? '✗' : '') }}
                        </div>
                        <span class="text-sm {{ ($userAnswer && $userAnswer->answer == 'true' || $question->answer == 'true') ? 'font-medium' : '' }}">
                            Benar
                            @if($userAnswer && $userAnswer->answer == 'true')
                                <span class="text-xs ml-2">(Jawaban Anda)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center {{ $question->answer == 'false' ? 'text-green-700' : ($userAnswer && $userAnswer->answer == 'false' && $question->answer != 'false' ? 'text-red-700' : 'text-gray-700') }}">
                        <div class="w-5 h-5 rounded-full 
                            {{ $question->answer == 'false' ? 'bg-green-500 text-white' : 
                              ($userAnswer && $userAnswer->answer == 'false' && $question->answer != 'false' ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                            flex items-center justify-center text-xs mr-2">
                            {{ $question->answer == 'false' ? '✓' : ($userAnswer && $userAnswer->answer == 'false' && $question->answer != 'false' ? '✗' : '') }}
                        </div>
                        <span class="text-sm {{ ($userAnswer && $userAnswer->answer == 'false' || $question->answer == 'false') ? 'font-medium' : '' }}">
                            Salah
                            @if($userAnswer && $userAnswer->answer == 'false')
                                <span class="text-xs ml-2">(Jawaban Anda)</span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @elseif($question->type == 'essay')
        <div class="mt-3 pl-4">
            @if(!$showAnswer)
                <textarea name="answer_{{ $question->id }}" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Masukkan jawaban Anda..." {{ $userAnswer ? 'disabled' : '' }}>{{ $userAnswer ? $userAnswer->answer_text : '' }}</textarea>
            @else
                <p class="text-sm font-medium text-gray-600">Jawaban Anda:</p>
                <div class="mt-2 bg-gray-50 p-3 rounded border border-gray-200">
                    <p class="text-sm text-gray-700">{{ $userAnswer ? $userAnswer->answer_text : '-' }}</p>
                </div>
                
                @if($question->answer_key && $showAnswer)
                    <p class="text-sm font-medium text-gray-600 mt-3">Kunci Jawaban / Kata Kunci:</p>
                    <div class="mt-1 bg-green-50 p-3 rounded border border-green-200">
                        <p class="text-sm text-green-700">{{ $question->answer_key }}</p>
                    </div>
                @endif
                
                @if($userAnswer && $userAnswer->score !== null)
                    <div class="mt-3 bg-blue-50 p-3 rounded border border-blue-200">
                        <p class="text-sm font-medium text-blue-800">Nilai: {{ $userAnswer->score }} / {{ $question->score }}</p>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
