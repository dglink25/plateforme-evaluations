<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Modifier la question : {{ $quiz->title }}
        </h2>
    </x-slot>

    @include('components.quizzes-nav')

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('questions.update', [$quiz, $question]) }}" id="question-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Type de question -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Type de question *
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" x-data="{ type: '{{ old('type', $question->type) }}' }">
                                    @foreach([
                                        'multiple_choice' => ['label' => 'QCM (1 réponse)', 'color' => 'blue'],
                                        'multiple_answer' => ['label' => 'Réponses multiples', 'color' => 'purple'],
                                        'text' => ['label' => 'Réponse texte', 'color' => 'green'],
                                        'file' => ['label' => 'Fichier', 'color' => 'yellow']
                                    ] as $value => $info)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="type" value="{{ $value }}" 
                                                   class="sr-only peer" 
                                                   x-model="type"
                                                   {{ old('type', $question->type) == $value ? 'checked' : '' }}>
                                            <div class="p-4 border-2 rounded-lg text-center transition-all
                                                        peer-checked:border-{{ $info['color'] }}-500 peer-checked:bg-{{ $info['color'] }}-50
                                                        hover:border-{{ $info['color'] }}-300 hover:bg-{{ $info['color'] }}-25">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $info['label'] }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Texte de la question -->
                            <div>
                                <label for="question_text" class="block text-sm font-medium text-gray-700">
                                    Question *
                                </label>
                                <textarea name="question_text" id="question_text" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                          required>{{ old('question_text', $question->question_text) }}</textarea>
                                @error('question_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Points -->
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700">
                                    Points *
                                </label>
                                <input type="number" name="points" id="points" min="1" max="100"
                                       class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       value="{{ old('points', $question->points) }}" required>
                                @error('points')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Options pour QCM et Réponses multiples -->
                            <div id="options-section" x-data="questionOptions()" x-show="$store.global.questionType === 'multiple_choice' || $store.global.questionType === 'multiple_answer'">
                                <div class="flex justify-between items-center mb-4">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Options de réponse
                                    </label>
                                    <button type="button" @click="addOption()"
                                            class="text-sm bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                        + Ajouter une option
                                    </button>
                                </div>
                                
                                <div class="space-y-3" id="options-container">
                                    <!-- Les options seront ajoutées ici dynamiquement -->
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded">
                                            <span class="text-gray-500" x-text="String.fromCharCode(65 + index)"></span>
                                            <input type="text" x-model="options[index]" 
                                                   :name="'options[' + index + ']'"
                                                   placeholder="Texte de l'option"
                                                   class="flex-1 border-gray-300 rounded shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                            
                                            <!-- Pour QCM (1 réponse) -->
                                            <template x-if="$store.global.questionType === 'multiple_choice'">
                                                <div class="flex items-center">
                                                    @php
                                                        $isCorrect = $question->type === 'multiple_choice' && $question->correct_answer == 'optionIndex';
                                                    @endphp
                                                    <input type="radio" name="correct_answer" :value="index" 
                                                           :id="'correct_' + index"
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                           :checked="{{ $question->type === 'multiple_choice' && $question->correct_answer == 'loopIndex' ? 'true' : 'false' }}">
                                                    <label :for="'correct_' + index" class="ml-2 text-sm text-gray-700">
                                                        Bonne réponse
                                                    </label>
                                                </div>
                                            </template>
                                            
                                            <!-- Pour réponses multiples -->
                                            <template x-if="$store.global.questionType === 'multiple_answer'">
                                                <div class="flex items-center">
                                                    @php
                                                        $isCorrectMulti = $question->type === 'multiple_answer' && in_array('optionIndex', (array)$question->correct_answer);
                                                    @endphp
                                                    <input type="checkbox" :name="'correct_answers[]'" :value="index" 
                                                           :id="'correct_multi_' + index"
                                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300"
                                                           :checked="{{ $isCorrectMulti ? 'true' : 'false' }}">
                                                    <label :for="'correct_multi_' + index" class="ml-2 text-sm text-gray-700">
                                                        Correct
                                                    </label>
                                                </div>
                                            </template>
                                            
                                            <button type="button" @click="removeOption(index)"
                                                    class="text-red-500 hover:text-red-700"
                                                    x-show="options.length > 2">
                                                ✕
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Messages d'erreur pour les options -->
                                <div id="options-error" class="mt-2 text-sm text-red-600 hidden">
                                    Au moins 2 options sont requises pour ce type de question.
                                </div>
                            </div>

                            <!-- Réponse modèle pour texte -->
                            <div id="text-answer-section" x-show="$store.global.questionType === 'text'">
                                <label for="correct_answer_text" class="block text-sm font-medium text-gray-700">
                                    Réponse modèle (pour la correction)
                                </label>
                                <textarea name="correct_answer_text" id="correct_answer_text" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('correct_answer_text', $question->type === 'text' ? ($question->correct_answer['text'] ?? '') : '') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    Cette réponse sera utilisée comme référence pour la correction manuelle.
                                </p>
                            </div>

                            <!-- Pas de champ supplémentaire pour fichier -->
                            <div id="file-section" x-show="$store.global.questionType === 'file'">
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Question de type fichier
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>
                                                    L'apprenant pourra télécharger un fichier (PDF, ZIP, image, etc.).
                                                    La correction se fera manuellement.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div>
                                    <a href="{{ route('quizzes.questions', $quiz) }}" 
                                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                        Annuler
                                    </a>
                                </div>
                                <div class="space-x-3">
                                    <button type="submit" 
                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store global pour le type de question
        document.addEventListener('alpine:init', () => {
            Alpine.store('global', {
                questionType: '{{ old('type', $question->type) }}'
            });
        });

        // Observer les changements de type
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                Alpine.store('global').questionType = this.value;
            });
        });

        // Gestion des options avec données existantes
        function questionOptions() {
            return {
                options: @json(old('options', $question->options ?? ['', ''])),
                addOption() {
                    this.options.push('');
                },
                removeOption(index) {
                    if (this.options.length > 2) {
                        this.options.splice(index, 1);
                    }
                }
            }
        }

        // Validation avant soumission
        document.getElementById('question-form').addEventListener('submit', function(e) {
            const type = Alpine.store('global').questionType;
            const optionsSection = document.getElementById('options-section');
            
            if ((type === 'multiple_choice' || type === 'multiple_answer') && optionsSection) {
                const options = Array.from(document.querySelectorAll('input[name^="options["]'))
                    .map(input => input.value.trim())
                    .filter(value => value !== '');
                
                if (options.length < 2) {
                    e.preventDefault();
                    document.getElementById('options-error').classList.remove('hidden');
                    optionsSection.scrollIntoView({ behavior: 'smooth' });
                    return false;
                }
            }
        });
    </script>
    @endpush
</x-app-layout>