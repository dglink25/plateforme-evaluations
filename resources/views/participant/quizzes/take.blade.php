<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $quiz->title }}
            </h2>
            
            <!-- Timer -->
            <div id="timer" class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="time-display" class="text-lg font-bold text-gray-900">
                    {{ gmdate('H:i:s', $remainingSeconds) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alertes -->
            <div id="auto-save-alert" class="hidden mb-4 p-3 bg-blue-100 text-blue-700 rounded-lg text-sm">
                <div class="flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Réponses sauvegardées automatiquement</span>
                </div>
            </div>

            <form id="quiz-form" method="POST" action="{{ route('participant.quizzes.finish', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                  enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sidebar avec progression -->
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow-sm sm:rounded-lg sticky top-6">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Progression</h3>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Questions répondues</span>
                                        <span id="answered-count">0</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-6">
                                    @foreach($questions as $index => $question)
                                        <a href="#question-{{ $question->id }}" 
                                           class="block p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors question-nav"
                                           data-question-id="{{ $question->id }}">
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center justify-center h-6 w-6 text-xs font-medium rounded-full bg-gray-100 text-gray-800 mr-2">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <span class="text-sm text-gray-700 truncate">
                                                        {{ Str::limit($question->question_text, 30) }}
                                                    </span>
                                                </div>
                                                <div class="question-status" data-question-id="{{ $question->id }}">
                                                    <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                
                                <!-- Bouton de soumission -->
                                <button type="submit" 
                                        id="submit-btn"
                                        class="w-full py-3 px-4 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <div class="flex items-center justify-center">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Soumettre le quiz
                                    </div>
                                </button>
                                
                                <p class="mt-3 text-xs text-center text-gray-500">
                                    Le quiz se soumettra automatiquement à la fin du temps.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Questions -->
                    <div class="lg:col-span-3">
                        <div class="bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                @foreach($questions as $index => $question)
                                    <div id="question-{{ $question->id }}" class="question-container mb-10 pb-10 border-b border-gray-200 last:border-b-0">
                                        <div class="flex items-start mb-6">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 font-medium mr-3">
                                                {{ $index + 1 }}
                                            </span>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                                    {{ $question->question_text }}
                                                </h3>
                                                
                                                <!-- Points -->
                                                <div class="mb-6">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $question->points }} point(s)
                                                    </span>
                                                </div>
                                                
                                                <!-- Réponses selon le type -->
                                                <div class="mt-4">
                                                    @if($question->type === 'multiple_choice')
                                                        <div class="space-y-3">
                                                            @foreach($question->options as $optionIndex => $option)
                                                                <div class="flex items-center">
                                                                    <input type="radio" 
                                                                           id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                           name="answers[{{ $question->id }}]"
                                                                           value="{{ $optionIndex }}"
                                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 question-input"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ optional($question->answers->first())->answer_content == $optionIndex ? 'checked' : '' }}>
                                                                    <label for="question-{{ $question->id }}-option-{{ $optionIndex }}" 
                                                                           class="ml-3 block text-gray-700">
                                                                        <span class="inline-block mr-2 font-medium">{{ chr(65 + $optionIndex) }}.</span>
                                                                        {{ $option }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        
                                                    @elseif($question->type === 'multiple_answer')
                                                        <div class="space-y-3">
                                                            @foreach($question->options as $optionIndex => $option)
                                                                <div class="flex items-center">
                                                                    <input type="checkbox" 
                                                                           id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                           name="answers[{{ $question->id }}][]"
                                                                           value="{{ $optionIndex }}"
                                                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 question-input"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ in_array($optionIndex, (array)optional($question->answers->first())->answer_content ?? []) ? 'checked' : '' }}>
                                                                    <label for="question-{{ $question->id }}-option-{{ $optionIndex }}" 
                                                                           class="ml-3 block text-gray-700">
                                                                        <span class="inline-block mr-2 font-medium">{{ chr(65 + $optionIndex) }}.</span>
                                                                        {{ $option }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        
                                                    @elseif($question->type === 'text')
                                                        <div>
                                                            <textarea name="answers[{{ $question->id }}]"
                                                                      rows="4"
                                                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 question-input"
                                                                      data-question-id="{{ $question->id }}"
                                                                      placeholder="Tapez votre réponse ici...">{{ optional($question->answers->first())->answer_content ?? '' }}</textarea>
                                                        </div>
                                                        
                                                    @elseif($question->type === 'file')
                                                        <div class="space-y-4">
                                                            <div>
                                                                <input type="file" 
                                                                       name="files[{{ $question->id }}]"
                                                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 question-input"
                                                                       data-question-id="{{ $question->id }}"
                                                                       accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png">
                                                            </div>
                                                            
                                                            @if($question->answers->first() && $question->answers->first()->file_path)
                                                                <div class="p-3 bg-green-50 border border-green-200 rounded">
                                                                    <div class="flex items-center">
                                                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                        <span class="text-green-700">
                                                                            Fichier déjà uploadé: 
                                                                            <a href="{{ Storage::url($question->answers->first()->file_path) }}" 
                                                                               target="_blank" 
                                                                               class="underline hover:text-green-800">
                                                                                Télécharger
                                                                            </a>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialiser le timer
        let remainingSeconds = {{ $remainingSeconds }};
        let timerInterval;
        let autoSaveInterval;
        let hasSubmitted = false;

        // Formatage du temps
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Mettre à jour l'affichage du timer
        function updateTimerDisplay() {
            document.getElementById('time-display').textContent = formatTime(remainingSeconds);
            
            // Changer la couleur quand il reste peu de temps
            if (remainingSeconds <= 300) { // 5 minutes
                document.getElementById('timer').classList.add('text-red-600');
            }
        }

        // Démarrer le timer
        function startTimer() {
            updateTimerDisplay();
            
            timerInterval = setInterval(() => {
                remainingSeconds--;
                updateTimerDisplay();
                
                // Soumettre automatiquement quand le temps est écoulé
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    clearInterval(autoSaveInterval);
                    submitQuizAutomatically();
                }
            }, 1000);
        }

        // Soumettre automatiquement le quiz
        function submitQuizAutomatically() {
            if (hasSubmitted) return;
            
            hasSubmitted = true;
            
            // Sauvegarder les réponses une dernière fois
            saveAnswers().then(() => {
                // Soumettre le formulaire
                document.getElementById('quiz-form').submit();
            }).catch(error => {
                console.error('Erreur lors de la sauvegarde automatique:', error);
                document.getElementById('quiz-form').submit();
            });
        }

        // Sauvegarder les réponses automatiquement
        function setupAutoSave() {
            // Sauvegarder toutes les 30 secondes
            autoSaveInterval = setInterval(() => {
                saveAnswers();
            }, 30000); // 30 secondes
        }

        // Sauvegarder les réponses via AJAX
        async function saveAnswers() {
            const form = document.getElementById('quiz-form');
            const formData = new FormData(form);
            
            // Ne pas inclure le bouton de soumission
            formData.delete('_token');
            formData.delete('_method');
            
            try {
                const response = await fetch('{{ route("participant.quizzes.submit", ["quiz" => $quiz, "participation" => $participation]) }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAutoSaveAlert();
                    updateProgress();
                }
                
                return data;
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
                return { success: false, message: 'Erreur réseau' };
            }
        }

        // Afficher l'alerte de sauvegarde automatique
        function showAutoSaveAlert() {
            const alert = document.getElementById('auto-save-alert');
            alert.classList.remove('hidden');
            alert.classList.add('flex');
            
            setTimeout(() => {
                alert.classList.remove('flex');
                alert.classList.add('hidden');
            }, 3000);
        }

        // Mettre à jour la barre de progression
        function updateProgress() {
            const answeredInputs = document.querySelectorAll('.question-input[type="radio"]:checked, .question-input[type="checkbox"]:checked, .question-input[type="text"]:not(:empty), .question-input[type="textarea"]:not(:empty), .question-input[type="file"]');
            const totalQuestions = {{ $questions->count() }};
            const answeredCount = answeredInputs.length;
            
            // Compter les textareas avec du contenu
            const textAreas = document.querySelectorAll('.question-input[type="textarea"], textarea.question-input');
            textAreas.forEach(textarea => {
                if (textarea.value.trim() !== '') {
                    answeredCount++;
                }
            });
            
            // Mettre à jour le compteur
            document.getElementById('answered-count').textContent = answeredCount;
            
            // Mettre à jour la barre de progression
            const progressPercentage = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
            document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
            
            // Mettre à jour les indicateurs de question
            document.querySelectorAll('.question-nav').forEach(nav => {
                const questionId = nav.dataset.questionId;
                const questionInputs = document.querySelectorAll(`.question-input[data-question-id="${questionId}"]`);
                let isAnswered = false;
                
                questionInputs.forEach(input => {
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        if (input.checked) isAnswered = true;
                    } else if (input.type === 'file') {
                        if (input.files.length > 0) isAnswered = true;
                    } else {
                        if (input.value.trim() !== '') isAnswered = true;
                    }
                });
                
                const statusIndicator = nav.querySelector('.question-status div');
                if (isAnswered) {
                    statusIndicator.classList.remove('bg-gray-300');
                    statusIndicator.classList.add('bg-green-500');
                } else {
                    statusIndicator.classList.remove('bg-green-500');
                    statusIndicator.classList.add('bg-gray-300');
                }
            });
        }

        // Écouter les changements dans les réponses
        document.addEventListener('DOMContentLoaded', function() {
            // Démarrer le timer
            startTimer();
            
            // Démarrer la sauvegarde automatique
            setupAutoSave();
            
            // Initialiser la progression
            updateProgress();
            
            // Écouter les changements dans les réponses
            document.querySelectorAll('.question-input').forEach(input => {
                input.addEventListener('change', function() {
                    // Sauvegarder cette réponse
                    saveAnswersForQuestion(this.dataset.questionId);
                    updateProgress();
                });
                
                // Pour les textareas
                if (input.tagName === 'TEXTAREA') {
                    input.addEventListener('input', debounce(function() {
                        saveAnswersForQuestion(this.dataset.questionId);
                        updateProgress();
                    }, 1000));
                }
            });
            
            // Empêcher la soumission multiple
            document.getElementById('quiz-form').addEventListener('submit', function(e) {
                if (hasSubmitted) {
                    e.preventDefault();
                    return false;
                }
                
                const confirmSubmit = confirm('Êtes-vous sûr de vouloir soumettre votre quiz ? Cette action est irréversible.');
                if (!confirmSubmit) {
                    e.preventDefault();
                    return false;
                }
                
                hasSubmitted = true;
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').innerHTML = `
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Soumission en cours...
                    </div>
                `;
            });
            
            // Navigation entre questions
            document.querySelectorAll('.question-nav').forEach(nav => {
                nav.addEventListener('click', function(e) {
                    e.preventDefault();
                    const questionId = this.dataset.questionId;
                    const questionElement = document.getElementById(`question-${questionId}`);
                    
                    if (questionElement) {
                        questionElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
        
        // Sauvegarder les réponses pour une question spécifique
        async function saveAnswersForQuestion(questionId) {
            const formData = new FormData();
            
            // Ajouter le token CSRF
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Ajouter les réponses pour cette question
            const questionInputs = document.querySelectorAll(`.question-input[data-question-id="${questionId}"]`);
            
            questionInputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    if (input.checked) {
                        if (input.type === 'checkbox') {
                            if (!formData.has(`answers[${questionId}][]`)) {
                                formData.append(`answers[${questionId}][]`, input.value);
                            } else {
                                // Pour les checkboxes, on doit gérer les valeurs multiples
                                const values = formData.getAll(`answers[${questionId}][]`);
                                values.push(input.value);
                                formData.delete(`answers[${questionId}][]`);
                                values.forEach(val => formData.append(`answers[${questionId}][]`, val));
                            }
                        } else {
                            formData.append(`answers[${questionId}]`, input.value);
                        }
                    }
                } else if (input.type === 'file') {
                    if (input.files.length > 0) {
                        formData.append(`files[${questionId}]`, input.files[0]);
                    }
                } else {
                    formData.append(`answers[${questionId}]`, input.value);
                }
            });
            
            try {
                await fetch('{{ route("participant.quizzes.submit", ["quiz" => $quiz, "participation" => $participation]) }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                showAutoSaveAlert();
            } catch (error) {
                console.error('Erreur lors de la sauvegarde:', error);
            }
        }
        
        // Fonction debounce pour limiter les appels
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Empêcher la fermeture de la page pendant le quiz
        window.addEventListener('beforeunload', function(e) {
            if (!hasSubmitted && remainingSeconds > 0) {
                e.preventDefault();
                e.returnValue = 'Vous êtes en train de passer un quiz. Si vous quittez, vos réponses pourraient être perdues.';
                return e.returnValue;
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .question-nav {
            transition: all 0.2s ease;
        }
        
        .question-nav:hover {
            transform: translateY(-2px);
        }
        
        .question-status div {
            transition: background-color 0.3s ease;
        }
        
        #progress-bar {
            transition: width 0.5s ease;
        }
        
        .sticky {
            position: -webkit-sticky;
            position: sticky;
        }
    </style>
    @endpush
</x-app-layout> 