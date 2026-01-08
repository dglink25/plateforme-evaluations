@extends('layouts.app')

@section('title', 'Quiz : ' . $quiz->title)

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête fixe -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $quiz->title }}</h5>
                    <small class="text-muted">{{ $quiz->questions->count() }} questions • {{ $quiz->duration }} minutes</small>
                </div>
                
                <!-- Timer -->
                <div id="timer" class="d-flex align-items-center">
                    <i class="bi bi-clock fs-5 text-danger me-2"></i>
                    <span id="time-display" class="fs-4 fw-bold text-danger">
                        {{ gmdate('H:i:s', $remainingSeconds) }}
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container pt-5 mt-5">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 80px;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Progression</h6>
                            
                            <!-- Barre de progression -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Questions répondues</small>
                                    <small id="answered-count">0</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                            <!-- Navigation des questions -->
                            <div class="mb-4">
                                <small class="text-muted d-block mb-2">Navigation rapide</small>
                                <div class="row g-2" id="question-nav-grid">
                                    @foreach($questions as $index => $question)
                                        <div class="col-4">
                                            <a href="#question-{{ $question->id }}" 
                                               class="d-block text-center p-2 border rounded question-nav"
                                               data-question-id="{{ $question->id }}"
                                               title="{{ Str::limit($question->question_text, 30) }}">
                                                <div class="question-number">{{ $index + 1 }}</div>
                                                <div class="question-status mt-1">
                                                    <div class="status-indicator" data-question-id="{{ $question->id }}"></div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Bouton de soumission -->
                            <button type="button" 
                                    id="submit-btn"
                                    class="btn btn-success w-100"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#submitModal">
                                <i class="bi bi-check-circle me-2"></i>
                                Soumettre le quiz
                            </button>
                            
                            <small class="text-muted d-block mt-2 text-center">
                                Le quiz se soumettra automatiquement à la fin du temps
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Questions -->
            <div class="col-lg-9">
                <div class="px-3 px-lg-4">
                    <!-- Alerte sauvegarde -->
                    <div id="auto-save-alert" class="alert alert-info alert-dismissible fade show mb-4 d-none" role="alert">
                        <i class="bi bi-save me-2"></i>
                        Réponses sauvegardées automatiquement
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <!-- Formulaire -->
                    <form id="quiz-form" method="POST" action="{{ route('participant.quizzes.finish', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                          enctype="multipart/form-data">
                        @csrf
                        
                        @foreach($questions as $index => $question)
                            <div id="question-{{ $question->id }}" class="card mb-4 question-container">
                                <div class="card-body">
                                    <!-- En-tête question -->
                                    <div class="d-flex align-items-start mb-4">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px; flex-shrink: 0;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-3">{{ $question->question_text }}</h5>
                                            
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <span class="badge bg-primary">
                                                    {{ $question->points }} point(s)
                                                </span>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                                </span>
                                            </div>
                                            
                                            <!-- Réponses -->
                                            <div class="mt-3">
                                                @if($question->type === 'multiple_choice')
                                                    <div class="list-group list-group-flush">
                                                        @foreach($question->options as $optionIndex => $option)
                                                            <label class="list-group-item list-group-item-action">
                                                                <div class="form-check">
                                                                    <input type="radio" 
                                                                           id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                           name="answers[{{ $question->id }}]"
                                                                           value="{{ $optionIndex }}"
                                                                           class="form-check-input question-input"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ optional($question->answers->first())->answer_content == $optionIndex ? 'checked' : '' }}>
                                                                    <label class="form-check-label d-flex align-items-center" 
                                                                           for="question-{{ $question->id }}-option-{{ $optionIndex }}">
                                                                        <span class="fw-bold me-3">{{ chr(65 + $optionIndex) }}.</span>
                                                                        <span>{{ $option }}</span>
                                                                    </label>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    
                                                @elseif($question->type === 'multiple_answer')
                                                    <div class="list-group list-group-flush">
                                                        @foreach($question->options as $optionIndex => $option)
                                                            <label class="list-group-item">
                                                                <div class="form-check">
                                                                    <input type="checkbox" 
                                                                           id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                           name="answers[{{ $question->id }}][]"
                                                                           value="{{ $optionIndex }}"
                                                                           class="form-check-input question-input"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ in_array($optionIndex, (array)optional($question->answers->first())->answer_content ?? []) ? 'checked' : '' }}>
                                                                    <label class="form-check-label d-flex align-items-center" 
                                                                           for="question-{{ $question->id }}-option-{{ $optionIndex }}">
                                                                        <span class="fw-bold me-3">{{ chr(65 + $optionIndex) }}.</span>
                                                                        <span>{{ $option }}</span>
                                                                    </label>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    
                                                @elseif($question->type === 'text')
                                                    <div>
                                                        <textarea name="answers[{{ $question->id }}]"
                                                                  rows="5"
                                                                  class="form-control question-input"
                                                                  data-question-id="{{ $question->id }}"
                                                                  placeholder="Tapez votre réponse ici...">{{ optional($question->answers->first())->answer_content ?? '' }}</textarea>
                                                    </div>
                                                    
                                                @elseif($question->type === 'file')
                                                    <div>
                                                        <div class="mb-3">
                                                            <input type="file" 
                                                                   name="files[{{ $question->id }}]"
                                                                   class="form-control question-input"
                                                                   data-question-id="{{ $question->id }}"
                                                                   accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png,.txt">
                                                        </div>
                                                        
                                                        @if($question->answers->first() && $question->answers->first()->file_path)
                                                            <div class="alert alert-success d-flex align-items-center">
                                                                <i class="bi bi-check-circle-fill me-2"></i>
                                                                <div>
                                                                    Fichier déjà uploadé: 
                                                                    <a href="{{ Storage::url($question->answers->first()->file_path) }}" 
                                                                       target="_blank" 
                                                                       class="alert-link">
                                                                        Télécharger
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Navigation entre questions -->
                                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                        @if($index > 0)
                                            <a href="#question-{{ $questions[$index - 1]->id }}" 
                                               class="btn btn-outline-secondary btn-prev">
                                                <i class="bi bi-chevron-left me-2"></i>Question précédente
                                            </a>
                                        @else
                                            <div></div>
                                        @endif
                                        
                                        @if($index < $questions->count() - 1)
                                            <a href="#question-{{ $questions[$index + 1]->id }}" 
                                               class="btn btn-primary btn-next">
                                                Question suivante <i class="bi bi-chevron-right ms-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la soumission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir soumettre votre quiz ?</p>
                <p class="text-muted small">Cette action est irréversible. Assurez-vous d'avoir répondu à toutes les questions.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="confirm-submit" class="btn btn-success">Soumettre</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variables globales
    let remainingSeconds = {{ $remainingSeconds }};
    let timerInterval;
    let autoSaveInterval;
    let hasSubmitted = false;
    let saveQueue = [];

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
        const timerElement = document.getElementById('timer');
        if (remainingSeconds <= 300) { // 5 minutes
            timerElement.classList.remove('text-danger');
            timerElement.classList.add('text-danger');
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

    // Configuration de la sauvegarde automatique
    function setupAutoSave() {
        // Sauvegarder toutes les 30 secondes
        autoSaveInterval = setInterval(() => {
            if (saveQueue.length === 0) {
                saveAllAnswers();
            }
        }, 30000);
    }

    // Sauvegarder toutes les réponses
    async function saveAllAnswers() {
        const form = document.getElementById('quiz-form');
        const formData = new FormData(form);
        
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

    // Sauvegarder une réponse spécifique
    async function saveAnswer(questionId) {
        if (saveQueue.includes(questionId)) return;
        
        saveQueue.push(questionId);
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Collecter les réponses pour cette question
        const questionInputs = document.querySelectorAll(`.question-input[data-question-id="${questionId}"]`);
        let hasAnswer = false;
        
        questionInputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.checked) {
                    if (input.type === 'checkbox') {
                        formData.append(`answers[${questionId}][]`, input.value);
                    } else {
                        formData.append(`answers[${questionId}]`, input.value);
                    }
                    hasAnswer = true;
                }
            } else if (input.type === 'file') {
                if (input.files.length > 0) {
                    formData.append(`files[${questionId}]`, input.files[0]);
                    hasAnswer = true;
                }
            } else {
                if (input.value.trim() !== '') {
                    formData.append(`answers[${questionId}]`, input.value);
                    hasAnswer = true;
                }
            }
        });
        
        // Ne pas sauvegarder si aucune réponse
        if (!hasAnswer) {
            saveQueue = saveQueue.filter(id => id !== questionId);
            return;
        }
        
        try {
            const response = await fetch('{{ route("participant.quizzes.submit", ["quiz" => $quiz, "participation" => $participation]) }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateProgress();
            }
            
        } catch (error) {
            console.error('Erreur lors de la sauvegarde:', error);
        } finally {
            saveQueue = saveQueue.filter(id => id !== questionId);
        }
    }

    // Afficher l'alerte de sauvegarde
    function showAutoSaveAlert() {
        const alert = document.getElementById('auto-save-alert');
        alert.classList.remove('d-none');
        
        setTimeout(() => {
            alert.classList.add('d-none');
        }, 3000);
    }

    // Mettre à jour la progression
    function updateProgress() {
        let answeredCount = 0;
        const totalQuestions = {{ $questions->count() }};
        
        // Compter les questions répondues
        document.querySelectorAll('.question-container').forEach(container => {
            const questionId = container.id.replace('question-', '');
            const questionInputs = container.querySelectorAll('.question-input');
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
            
            if (isAnswered) {
                answeredCount++;
                updateQuestionStatus(questionId, true);
            } else {
                updateQuestionStatus(questionId, false);
            }
        });
        
        // Mettre à jour le compteur et la barre de progression
        document.getElementById('answered-count').textContent = answeredCount;
        const progressPercentage = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
        document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
    }

    // Mettre à jour le statut d'une question
    function updateQuestionStatus(questionId, isAnswered) {
        const statusIndicator = document.querySelector(`.status-indicator[data-question-id="${questionId}"]`);
        if (statusIndicator) {
            statusIndicator.className = isAnswered ? 'status-indicator answered' : 'status-indicator';
        }
    }

    // Initialisation
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
                const questionId = this.dataset.questionId;
                saveAnswer(questionId);
                updateProgress();
            });
            
            // Pour les textareas
            if (input.tagName === 'TEXTAREA') {
                input.addEventListener('input', debounce(function() {
                    const questionId = this.dataset.questionId;
                    saveAnswer(questionId);
                    updateProgress();
                }, 1000));
            }
        });
        
        // Navigation entre questions
        document.querySelectorAll('.question-nav, .btn-prev, .btn-next').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href')?.startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Mettre à jour l'URL sans rechargement
                        history.pushState(null, '', targetId);
                    }
                }
            });
        });
        
        // Confirmation de soumission
        document.getElementById('confirm-submit').addEventListener('click', function() {
            if (hasSubmitted) return;
            
            hasSubmitted = true;
            document.getElementById('submit-btn').disabled = true;
            
            // Sauvegarder avant de soumettre
            saveAllAnswers().then(() => {
                document.getElementById('quiz-form').submit();
            });
        });
        
        // Empêcher la fermeture de la page
        window.addEventListener('beforeunload', function(e) {
            if (!hasSubmitted && remainingSeconds > 0) {
                e.preventDefault();
                e.returnValue = 'Vous êtes en train de passer un quiz. Si vous quittez, vos réponses pourraient être perdues.';
                return e.returnValue;
            }
        });
        
        // Gestion des fichiers
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files[0]?.name || 'Aucun fichier sélectionné';
                const label = this.nextElementSibling?.querySelector('.form-label') || 
                              this.parentElement.nextElementSibling;
                
                if (label) {
                    label.textContent = `Fichier sélectionné : ${fileName}`;
                }
            });
        });
    });

    // Fonction debounce
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
</script>

<style>
/* Styles spécifiques */
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #dee2e6;
    margin: 0 auto;
}

.status-indicator.answered {
    background-color: #198754;
}

.question-nav {
    text-decoration: none;
    color: #495057;
    transition: all 0.2s ease;
}

.question-nav:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.question-number {
    font-weight: 600;
    font-size: 0.9rem;
}

#timer {
    transition: color 0.3s ease;
}

.btn-next, .btn-prev {
    transition: all 0.2s ease;
}

.btn-next:hover, .btn-prev:hover {
    transform: translateY(-1px);
}

/* Scroll doux */
html {
    scroll-behavior: smooth;
}

/* Style pour les questions actives */
.question-container:target {
    border-left: 4px solid #0d6efd;
    padding-left: 1rem;
}
</style>
@endpush
@endsection