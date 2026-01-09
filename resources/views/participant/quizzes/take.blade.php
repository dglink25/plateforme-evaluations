@extends('layouts.app')

@section('title', 'Quiz : ' . $quiz->title)

@section('content')
<div class="container-fluid px-0">
    <!-- En-tête fixe -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top py-2">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center w-100 gap-2">
                <!-- Titre -->
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary me-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold text-truncate" style="max-width: 250px;">{{ $quiz->title }}</h5>
                        <small class="text-muted d-none d-md-block">{{ $quiz->questions->count() }} questions • {{ $quiz->duration }} minutes</small>
                        <small class="text-muted d-block d-md-none">{{ $quiz->questions->count() }} Q • {{ $quiz->duration }} min</small>
                    </div>
                </div>
                
                <!-- Timer et bouton mobile -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Timer -->
                    <div id="timer" class="d-flex align-items-center bg-light rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-clock fs-5 text-danger me-2"></i>
                        <span id="time-display" class="fs-4 fw-bold text-danger font-monospace">
                            {{ gmdate('H:i:s', $remainingSeconds) }}
                        </span>
                    </div>
                    
                    <!-- Bouton soumission mobile -->
                    <button type="button" 
                            id="submit-btn-mobile"
                            class="btn btn-success d-lg-none"
                            data-bs-toggle="modal" 
                            data-bs-target="#submitModal">
                        <i class="bi bi-check-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar Offcanvas pour mobile -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Navigation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('quizzes.partials.sidebar-mobile', ['quiz' => $quiz, 'questions' => $questions])
        </div>
    </div>

    <!-- Contenu principal -->
    <main class="container pt-5 mt-5">
        <div class="row g-0">
            <!-- Sidebar Desktop -->
            <aside class="col-lg-3 d-none d-lg-block">
                <div class="sticky-top" style="top: 80px;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @include('quizzes.partials.sidebar-desktop', ['quiz' => $quiz, 'questions' => $questions])
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Questions -->
            <section class="col-lg-9">
                <div class="px-2 px-md-3 px-lg-4">
                    <!-- Alerte sauvegarde -->
                    <div id="auto-save-alert" class="alert alert-info alert-dismissible fade show mb-3 mb-md-4 d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-save fs-5 me-2"></i>
                            <span class="flex-grow-1">Réponses sauvegardées automatiquement</span>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    </div>

                    <!-- Formulaire -->
                    <form id="quiz-form" method="POST" action="{{ route('participant.quizzes.finish', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                          enctype="multipart/form-data">
                        @csrf
                        
                        @foreach($questions as $index => $question)
                            <article id="question-{{ $question->id }}" class="card mb-3 mb-md-4 question-container shadow-sm">
                                <div class="card-body p-3 p-md-4">
                                    <!-- En-tête question -->
                                    <header class="mb-3 mb-md-4">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                 style="width: 45px; height: 45px;">
                                                <span class="fw-bold">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h3 class="h5 fw-bold mb-2">{{ $question->question_text }}</h3>
                                                
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <span class="badge bg-primary px-2 py-1">
                                                        <i class="bi bi-star-fill me-1"></i>{{ $question->points }} pts
                                                    </span>
                                                    <span class="badge bg-secondary px-2 py-1">
                                                        <i class="bi bi-type me-1"></i>{{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </header>
                                    
                                    <!-- Réponses -->
                                    <div class="mb-4">
                                        @if($question->type === 'multiple_choice')
                                            <div class="list-group list-group-flush border rounded overflow-hidden">
                                                @foreach($question->options as $optionIndex => $option)
                                                    <label class="list-group-item list-group-item-action border-bottom-0 py-3">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="radio" 
                                                                   id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                   name="answers[{{ $question->id }}]"
                                                                   value="{{ $optionIndex }}"
                                                                   class="form-check-input me-3"
                                                                   data-question-id="{{ $question->id }}"
                                                                   {{ optional($question->answers->first())->answer_content == $optionIndex ? 'checked' : '' }}>
                                                            <label class="form-check-label d-flex align-items-center w-100" 
                                                                   for="question-{{ $question->id }}-option-{{ $optionIndex }}">
                                                                <span class="fw-bold text-primary me-3">{{ chr(65 + $optionIndex) }}.</span>
                                                                <span>{{ $option }}</span>
                                                            </label>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            
                                        @elseif($question->type === 'multiple_answer')
                                            <div class="list-group list-group-flush border rounded overflow-hidden">
                                                @foreach($question->options as $optionIndex => $option)
                                                    <label class="list-group-item border-bottom-0 py-3">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input type="checkbox" 
                                                                   id="question-{{ $question->id }}-option-{{ $optionIndex }}"
                                                                   name="answers[{{ $question->id }}][]"
                                                                   value="{{ $optionIndex }}"
                                                                   class="form-check-input me-3"
                                                                   data-question-id="{{ $question->id }}"
                                                                   {{ in_array($optionIndex, (array)optional($question->answers->first())->answer_content ?? []) ? 'checked' : '' }}>
                                                            <label class="form-check-label d-flex align-items-center w-100" 
                                                                   for="question-{{ $question->id }}-option-{{ $optionIndex }}">
                                                                <span class="fw-bold text-primary me-3">{{ chr(65 + $optionIndex) }}.</span>
                                                                <span>{{ $option }}</span>
                                                            </label>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                            
                                        @elseif($question->type === 'text')
                                            <div class="form-floating">
                                                <textarea name="answers[{{ $question->id }}]"
                                                          id="textarea-{{ $question->id }}"
                                                          rows="5"
                                                          class="form-control h-auto"
                                                          data-question-id="{{ $question->id }}"
                                                          placeholder="Tapez votre réponse ici..."
                                                          style="min-height: 120px;">{{ optional($question->answers->first())->answer_content ?? '' }}</textarea>
                                                <label for="textarea-{{ $question->id }}">Votre réponse</label>
                                            </div>
                                            
                                        @elseif($question->type === 'file')
                                            <div class="border rounded p-3">
                                                <div class="mb-3">
                                                    <label for="file-{{ $question->id }}" class="form-label fw-semibold mb-2">
                                                        <i class="bi bi-upload me-2"></i>Télécharger un fichier
                                                    </label>
                                                    <input type="file" 
                                                           id="file-{{ $question->id }}"
                                                           name="files[{{ $question->id }}]"
                                                           class="form-control"
                                                           data-question-id="{{ $question->id }}"
                                                           accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png,.txt">
                                                    <div class="form-text">Formats acceptés : PDF, Word, ZIP, images, TXT</div>
                                                </div>
                                                
                                                @if($question->answers->first() && $question->answers->first()->file_path)
                                                    <div class="alert alert-success alert-dismissible fade show mb-0">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                                            <div class="flex-grow-1">
                                                                <strong>Fichier déjà uploadé</strong>
                                                                <div class="mt-1">
                                                                    <a href="{{ Storage::url($question->answers->first()->file_path) }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-outline-success">
                                                                        <i class="bi bi-download me-1"></i>Télécharger
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Navigation entre questions -->
                                    <footer class="d-flex justify-content-between mt-4 pt-3 border-top">
                                        @if($index > 0)
                                            <a href="#question-{{ $questions[$index - 1]->id }}" 
                                               class="btn btn-outline-secondary btn-prev btn-sm">
                                                <i class="bi bi-chevron-left me-1"></i><span class="d-none d-md-inline">Précédente</span>
                                            </a>
                                        @else
                                            <div></div>
                                        @endif
                                        
                                        @if($index < $questions->count() - 1)
                                            <a href="#question-{{ $questions[$index + 1]->id }}" 
                                               class="btn btn-primary btn-next btn-sm">
                                                <span class="d-none d-md-inline">Suivante</span><i class="bi bi-chevron-right ms-1"></i>
                                            </a>
                                        @else
                                            <button type="button" 
                                                    id="submit-btn-bottom"
                                                    class="btn btn-success"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#submitModal">
                                                <i class="bi bi-check-circle me-1"></i>Soumettre
                                            </button>
                                        @endif
                                    </footer>
                                </div>
                            </article>
                        @endforeach
                    </form>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="submitModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la soumission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-question-circle display-4 text-primary"></i>
                </div>
                <h6 class="fw-bold mb-2">Soumettre votre quiz ?</h6>
                <p class="text-muted mb-0">Assurez-vous d'avoir vérifié toutes vos réponses.</p>
                <p class="text-muted small">Cette action est irréversible.</p>
                
                <!-- Progression -->
                <div class="mt-4">
                    <small class="text-muted d-block mb-1">Questions répondues</small>
                    <div class="progress" style="height: 8px;">
                        <div id="modal-progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small id="modal-progress-text" class="text-muted mt-1">0/{{ $questions->count() }}</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Revenir au quiz</button>
                <button type="button" id="confirm-submit" class="btn btn-success px-4">
                    <i class="bi bi-check-circle me-2"></i>Soumettre
                </button>
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
    const totalQuestions = {{ $questions->count() }};

    // Formatage du temps en H:MM:SS
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        // Format H:MM:SS toujours avec 2 chiffres pour minutes et secondes
        return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Mettre à jour l'affichage du timer
    function updateTimerDisplay() {
        const timeDisplay = document.getElementById('time-display');
        if (timeDisplay) {
            timeDisplay.textContent = formatTime(remainingSeconds);
            
            // Effet visuel pour le dernier minuteur
            if (remainingSeconds <= 300) { // 5 minutes
                timeDisplay.classList.remove('text-danger');
                timeDisplay.classList.add('text-danger');
                
                // Clignotement pour les 30 dernières secondes
                if (remainingSeconds <= 30) {
                    timeDisplay.classList.toggle('blink');
                }
            }
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
    async function submitQuizAutomatically() {
        if (hasSubmitted) return;
        
        hasSubmitted = true;
        
        // Afficher un message
        showToast('Temps écoulé ! Soumission automatique en cours...', 'warning');
        
        // Sauvegarder les réponses une dernière fois
        try {
            await saveAllAnswers();
        } catch (error) {
            console.error('Erreur de sauvegarde:', error);
        }
        
        // Soumettre le formulaire
        setTimeout(() => {
            document.getElementById('quiz-form').submit();
        }, 1000);
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

    // Afficher l'alerte de sauvegarde
    function showAutoSaveAlert() {
        const alert = document.getElementById('auto-save-alert');
        if (alert) {
            alert.classList.remove('d-none');
            
            setTimeout(() => {
                alert.classList.add('d-none');
            }, 3000);
        }
    }

    // Afficher une notification toast
    function showToast(message, type = 'info') {
        // Créer le toast si nécessaire
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        // Supprimer après fermeture
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Mettre à jour la progression
    function updateProgress() {
        let answeredCount = 0;
        
        // Compter les questions répondues
        document.querySelectorAll('.question-container').forEach(container => {
            const questionId = container.id.replace('question-', '');
            const questionInputs = container.querySelectorAll('input, textarea');
            let isAnswered = false;
            
            questionInputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    if (input.checked) isAnswered = true;
                } else if (input.type === 'file') {
                    if (input.files.length > 0) isAnswered = true;
                } else if (input.tagName === 'TEXTAREA') {
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
        
        // Mettre à jour l'affichage
        const progressElement = document.getElementById('progress-bar');
        const counterElement = document.getElementById('answered-count');
        const modalProgressElement = document.getElementById('modal-progress-bar');
        const modalTextElement = document.getElementById('modal-progress-text');
        
        const progressPercentage = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
        
        if (progressElement) progressElement.style.width = `${progressPercentage}%`;
        if (counterElement) counterElement.textContent = answeredCount;
        if (modalProgressElement) modalProgressElement.style.width = `${progressPercentage}%`;
        if (modalTextElement) modalTextElement.textContent = `${answeredCount}/${totalQuestions}`;
    }

    // Mettre à jour le statut d'une question
    function updateQuestionStatus(questionId, isAnswered) {
        // Mettre à jour les indicateurs de statut
        document.querySelectorAll(`.question-status[data-question-id="${questionId}"] .status-indicator`).forEach(indicator => {
            indicator.className = `status-indicator ${isAnswered ? 'answered' : ''}`;
        });
        
        // Mettre à jour les liens de navigation
        const navLinks = document.querySelectorAll(`.question-nav[data-question-id="${questionId}"]`);
        navLinks.forEach(link => {
            if (isAnswered) {
                link.classList.add('answered');
            } else {
                link.classList.remove('answered');
            }
        });
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
        document.querySelectorAll('input, textarea').forEach(input => {
            const questionId = input.closest('.question-container')?.id.replace('question-', '');
            if (!questionId) return;
            
            input.addEventListener('change', function() {
                saveAnswer(questionId);
                updateProgress();
            });
            
            // Debounce pour les textareas
            if (input.tagName === 'TEXTAREA') {
                input.addEventListener('input', debounce(function() {
                    saveAnswer(questionId);
                    updateProgress();
                }, 1000));
            }
        });
        
        // Navigation fluide
        document.querySelectorAll('a[href^="#question-"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // Fermer l'offcanvas sur mobile
                    const offcanvas = document.getElementById('sidebarOffcanvas');
                    if (offcanvas) {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                        if (bsOffcanvas) bsOffcanvas.hide();
                    }
                    
                    // Scroll fluide
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // URL
                    history.replaceState(null, '', targetId);
                }
            });
        });
        
        // Confirmation de soumission
        document.getElementById('confirm-submit').addEventListener('click', function() {
            if (hasSubmitted) return;
            
            hasSubmitted = true;
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            
            // Désactiver le bouton et montrer l'indicateur de chargement
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Soumission...';
            
            // Sauvegarder avant de soumettre
            saveAllAnswers().finally(() => {
                document.getElementById('quiz-form').submit();
            });
        });
        
        // Mettre à jour la progression dans le modal
        const submitModal = document.getElementById('submitModal');
        if (submitModal) {
            submitModal.addEventListener('show.bs.modal', updateProgress);
        }
        
        // Empêcher la fermeture de la page
        window.addEventListener('beforeunload', function(e) {
            if (!hasSubmitted && remainingSeconds > 0) {
                e.preventDefault();
                e.returnValue = 'Vos réponses pourraient être perdues si vous quittez cette page.';
                return e.returnValue;
            }
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

    // Fonction de sauvegarde individuelle
    async function saveAnswer(questionId) {
        // Implémentation simplifiée - ajustez selon vos besoins
        // Cette fonction devrait envoyer les réponses au serveur
    }
</script>

<style>
/* Styles améliorés */
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
}

/* Header fixe */
header.navbar {
    min-height: 70px;
}

/* Timer amélioré */
#timer {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border: 2px solid #ff6b6b;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
    transition: all 0.3s ease;
}

#timer:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
}

#time-display {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Animation clignotante */
.blink {
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* Questions */
.question-container {
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.question-container:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.question-container:target {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.1);
}

/* Options */
.list-group-item {
    border: 1px solid #dee2e6;
    margin-bottom: -1px;
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(4px);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Navigation */
.question-nav {
    text-decoration: none;
    color: #495057;
    border: 2px solid transparent;
    border-radius: 8px;
    padding: 8px;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.question-nav:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.question-nav.answered {
    background-color: rgba(25, 135, 84, 0.1);
    border-color: var(--success-color);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #dee2e6;
    margin: 4px auto 0;
    transition: all 0.3s ease;
}

.status-indicator.answered {
    background-color: var(--success-color);
    box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2);
}

/* Boutons */
.btn {
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-next, .btn-prev {
    min-width: 120px;
}

.btn-next:hover, .btn-prev:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    header.navbar {
        min-height: 60px;
        padding: 8px 0;
    }
    
    #timer {
        padding: 6px 12px;
    }
    
    #time-display {
        font-size: 1.2rem !important;
    }
    
    .question-container .card-body {
        padding: 1rem !important;
    }
    
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .btn-next, .btn-prev {
        min-width: auto;
    }
}

@media (max-width: 576px) {
    #timer {
        padding: 4px 10px;
    }
    
    #time-display {
        font-size: 1.1rem !important;
    }
    
    .question-nav {
        padding: 6px;
    }
    
    .question-number {
        font-size: 0.8rem;
    }
}

/* Scroll personnalisé */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Animation pour les sauvegardes */
@keyframes savePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.auto-saving {
    animation: savePulse 0.5s ease;
}

/* Style pour les questions actives dans la sidebar */
.question-nav.active {
    background-color: var(--primary-color);
    color: white;
}
</style>
@endpush

<!-- Partials inclus -->
@section('partials')
    @include('quizzes.partials.sidebar-desktop')
    @include('quizzes.partials.sidebar-mobile')
@endsection