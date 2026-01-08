@extends('layouts.app')

@section('title', 'Modifier la question : ' . $quiz->title)

@section('header', 'Modifier la question : ' . $quiz->title)

@section('content')
<div class="container py-4">
    <!-- Navigation des quizzes -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('quizzes.index') }}">
            <i class="bi bi-list-check me-2"></i>Mes Évaluations
        </a>
        <a class="nav-link" href="{{ route('quizzes.questions', $quiz) }}">
            <i class="bi bi-question-circle me-2"></i>Questions
        </a>
        <a class="nav-link active" href="#">
            <i class="bi bi-pencil me-2"></i>Modifier Question
        </a>
        <a class="nav-link" href="{{ route('participant.quizzes.index') }}">
            <i class="bi bi-clock me-2"></i>Passer un Quiz
        </a>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Modifier la question</h5>
                        <a href="{{ route('quizzes.questions', $quiz) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Retour aux questions
                        </a>
                    </div>

                    <form method="POST" action="{{ route('questions.update', [$quiz, $question]) }}" id="question-form">
                        @csrf
                        @method('PUT')

                        <!-- Type de question -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>Type de question *
                            </label>
                            <div class="row g-3">
                                @foreach([
                                    'multiple_choice' => ['label' => 'QCM (1 réponse)', 'icon' => 'bi-1-circle', 'color' => 'primary'],
                                    'multiple_answer' => ['label' => 'Réponses multiples', 'icon' => 'bi-check2-all', 'color' => 'purple'],
                                    'text' => ['label' => 'Réponse texte', 'icon' => 'bi-textarea-t', 'color' => 'success'],
                                    'file' => ['label' => 'Fichier', 'icon' => 'bi-file-earmark-arrow-up', 'color' => 'warning']
                                ] as $value => $info)
                                    <div class="col-md-3 col-6">
                                        <div class="form-check card-select">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="type" 
                                                   id="type_{{ $value }}" 
                                                   value="{{ $value }}"
                                                   {{ old('type', $question->type) == $value ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="type_{{ $value }}">
                                                <div class="card border h-100 text-center p-3 hover-shadow">
                                                    <div class="mb-2">
                                                        <i class="bi {{ $info['icon'] }} fs-3 text-{{ $info['color'] }}"></i>
                                                    </div>
                                                    <div class="fw-medium">{{ $info['label'] }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Texte de la question -->
                        <div class="mb-4">
                            <label for="question_text" class="form-label fw-semibold">
                                <i class="bi bi-chat-square-text me-1"></i>Question *
                            </label>
                            <textarea name="question_text" 
                                      id="question_text" 
                                      rows="3"
                                      class="form-control @error('question_text') is-invalid @enderror"
                                      required>{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Points -->
                        <div class="mb-4">
                            <label for="points" class="form-label fw-semibold">
                                <i class="bi bi-star me-1"></i>Points *
                            </label>
                            <div class="input-group" style="width: 150px;">
                                <input type="number" 
                                       name="points" 
                                       id="points" 
                                       min="1" 
                                       max="100"
                                       class="form-control @error('points') is-invalid @enderror"
                                       value="{{ old('points', $question->points) }}" 
                                       required>
                                <span class="input-group-text">point(s)</span>
                            </div>
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Section Options -->
                        <div id="options-section" class="mb-4 {{ in_array($question->type, ['multiple_choice', 'multiple_answer']) ? '' : 'd-none' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold mb-0">
                                    <i class="bi bi-list-check me-1"></i>Options de réponse
                                </label>
                                <button type="button" id="add-option" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter une option
                                </button>
                            </div>
                            
                            <div id="options-container" class="space-y-2">
                                @if(in_array($question->type, ['multiple_choice', 'multiple_answer']) && !empty($question->options))
                                    @foreach($question->options as $index => $option)
                                        <div class="option-item border rounded p-3">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-3">{{ chr(65 + $index) }}</span>
                                                <input type="text" 
                                                       name="options[{{ $index }}]" 
                                                       class="form-control me-3" 
                                                       value="{{ old('options.' . $index, $option) }}"
                                                       placeholder="Texte de l'option">
                                                <div class="form-check me-3">
                                                    @if($question->type === 'multiple_choice')
                                                        <input class="form-check-input correct-answer" 
                                                               type="radio" 
                                                               name="correct_answer" 
                                                               value="{{ $index }}" 
                                                               id="correct_{{ $index }}"
                                                               {{ old('correct_answer', $question->correct_answer) == $index ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="correct_{{ $index }}">
                                                            Bonne réponse
                                                        </label>
                                                    @else
                                                        <input class="form-check-input correct-answer" 
                                                               type="checkbox" 
                                                               name="correct_answers[]" 
                                                               value="{{ $index }}" 
                                                               id="correct_{{ $index }}"
                                                               {{ in_array($index, (array)old('correct_answers', $question->correct_answer ?? [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="correct_{{ $index }}">
                                                            Correct
                                                        </label>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-option"
                                                        {{ count($question->options) <= 2 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Options par défaut -->
                                    <div class="option-item border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary me-3">A</span>
                                            <input type="text" 
                                                   name="options[0]" 
                                                   class="form-control me-3" 
                                                   value="{{ old('options.0') }}"
                                                   placeholder="Texte de l'option">
                                            <div class="form-check me-3">
                                                <input class="form-check-input correct-answer" 
                                                       type="radio" 
                                                       name="correct_answer" 
                                                       value="0" 
                                                       id="correct_0">
                                                <label class="form-check-label" for="correct_0">
                                                    Bonne réponse
                                                </label>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-option" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="option-item border rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary me-3">B</span>
                                            <input type="text" 
                                                   name="options[1]" 
                                                   class="form-control me-3" 
                                                   value="{{ old('options.1') }}"
                                                   placeholder="Texte de l'option">
                                            <div class="form-check me-3">
                                                <input class="form-check-input correct-answer" 
                                                       type="radio" 
                                                       name="correct_answer" 
                                                       value="1" 
                                                       id="correct_1">
                                                <label class="form-check-label" for="correct_1">
                                                    Bonne réponse
                                                </label>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div id="multiple-answers-container" class="{{ $question->type === 'multiple_answer' ? '' : 'd-none' }}">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Cochez toutes les bonnes réponses :
                                </p>
                            </div>
                        </div>

                        <!-- Section Réponse texte -->
                        <div id="text-answer-section" class="mb-4 {{ $question->type === 'text' ? '' : 'd-none' }}">
                            <label for="correct_answer_text" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-1"></i>Réponse modèle (pour la correction)
                            </label>
                            <textarea name="correct_answer_text" 
                                      id="correct_answer_text" 
                                      rows="4"
                                      class="form-control">{{ old('correct_answer_text', $question->type === 'text' && isset($question->correct_answer['text']) ? $question->correct_answer['text'] : '') }}</textarea>
                            <div class="form-text">
                                Cette réponse sera utilisée comme référence pour la correction manuelle.
                            </div>
                        </div>

                        <!-- Section Fichier -->
                        <div id="file-section" class="mb-4 {{ $question->type === 'file' ? '' : 'd-none' }}">
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle fs-4 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading">Question de type fichier</h6>
                                        <p class="mb-0">
                                            L'apprenant pourra télécharger un fichier (PDF, ZIP, image, etc.).
                                            La correction se fera manuellement.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('quizzes.questions', $quiz) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fonction pour mettre à jour les sections visibles
    function updateSections() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        const optionsSection = document.getElementById('options-section');
        const textAnswerSection = document.getElementById('text-answer-section');
        const fileSection = document.getElementById('file-section');
        const multipleAnswersContainer = document.getElementById('multiple-answers-container');
        
        // Masquer toutes les sections d'abord
        optionsSection.classList.add('d-none');
        textAnswerSection.classList.add('d-none');
        fileSection.classList.add('d-none');
        
        // Afficher la section appropriée
        if (selectedType === 'multiple_choice' || selectedType === 'multiple_answer') {
            optionsSection.classList.remove('d-none');
            
            // Pour QCM, afficher les boutons radio
            if (selectedType === 'multiple_choice') {
                document.querySelectorAll('.correct-answer').forEach(el => {
                    el.type = 'radio';
                    el.name = 'correct_answer';
                });
                multipleAnswersContainer.classList.add('d-none');
            }
            // Pour réponses multiples, afficher les checkboxes
            else if (selectedType === 'multiple_answer') {
                document.querySelectorAll('.correct-answer').forEach(el => {
                    el.type = 'checkbox';
                    el.name = 'correct_answers[]';
                });
                multipleAnswersContainer.classList.remove('d-none');
            }
        } else if (selectedType === 'text') {
            textAnswerSection.classList.remove('d-none');
        } else if (selectedType === 'file') {
            fileSection.classList.remove('d-none');
        }
    }

    // Initialiser les sections
    document.addEventListener('DOMContentLoaded', function() {
        updateSections();
        
        // Écouter les changements de type
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', updateSections);
        });
        
        // Gestion des options
        let optionCount = {{ in_array($question->type, ['multiple_choice', 'multiple_answer']) && !empty($question->options) ? count($question->options) : 2 }};
        const optionLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        // Ajouter une option
        document.getElementById('add-option')?.addEventListener('click', function() {
            if (optionCount >= optionLetters.length) return;
            
            const container = document.getElementById('options-container');
            const optionItem = document.createElement('div');
            optionItem.className = 'option-item border rounded p-3 mt-2';
            
            optionItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary me-3">${optionLetters[optionCount]}</span>
                    <input type="text" 
                           name="options[${optionCount}]" 
                           class="form-control me-3" 
                           placeholder="Texte de l'option">
                    <div class="form-check me-3">
                        <input class="form-check-input correct-answer" 
                               type="${document.querySelector('input[name="type"]:checked').value === 'multiple_choice' ? 'radio' : 'checkbox'}" 
                               name="${document.querySelector('input[name="type"]:checked').value === 'multiple_choice' ? 'correct_answer' : 'correct_answers[]'}" 
                               value="${optionCount}" 
                               id="correct_${optionCount}">
                        <label class="form-check-label" for="correct_${optionCount}">
                            ${document.querySelector('input[name="type"]:checked').value === 'multiple_choice' ? 'Bonne réponse' : 'Correct'}
                        </label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(optionItem);
            optionCount++;
            
            // Activer les boutons de suppression
            document.querySelectorAll('.remove-option').forEach(btn => {
                btn.disabled = false;
            });
        });
        
        // Supprimer une option
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') || 
                e.target.closest('.remove-option')) {
                const btn = e.target.classList.contains('remove-option') ? e.target : e.target.closest('.remove-option');
                const optionItem = btn.closest('.option-item');
                
                if (document.querySelectorAll('.option-item').length > 2) {
                    optionItem.remove();
                    optionCount--;
                    
                    // Mettre à jour les lettres et les valeurs
                    updateOptionLabels();
                    
                    // Désactiver le bouton de suppression s'il ne reste que 2 options
                    if (document.querySelectorAll('.option-item').length <= 2) {
                        document.querySelectorAll('.remove-option').forEach(btn => {
                            btn.disabled = true;
                        });
                    }
                }
            }
        });
        
        // Mettre à jour les labels des options
        function updateOptionLabels() {
            const items = document.querySelectorAll('.option-item');
            items.forEach((item, index) => {
                const badge = item.querySelector('.badge');
                const input = item.querySelector('input[name^="options"]');
                const correctInput = item.querySelector('.correct-answer');
                
                badge.textContent = optionLetters[index];
                badge.setAttribute('data-index', index);
                
                if (input) {
                    input.name = `options[${index}]`;
                }
                
                if (correctInput) {
                    correctInput.value = index;
                    correctInput.id = `correct_${index}`;
                    correctInput.nextElementSibling.htmlFor = `correct_${index}`;
                }
            });
        }
    });

    // Validation du formulaire
    document.getElementById('question-form').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type"]:checked').value;
        
        // Validation pour QCM et réponses multiples
        if (type === 'multiple_choice' || type === 'multiple_answer') {
            const options = document.querySelectorAll('input[name^="options"]');
            const filledOptions = Array.from(options).filter(opt => opt.value.trim() !== '');
            
            if (filledOptions.length < 2) {
                e.preventDefault();
                alert('Veuillez remplir au moins 2 options de réponse.');
                return false;
            }
            
            // Validation pour QCM : une réponse sélectionnée
            if (type === 'multiple_choice') {
                const selected = document.querySelector('input[name="correct_answer"]:checked');
                if (!selected) {
                    e.preventDefault();
                    alert('Veuillez sélectionner la bonne réponse pour cette question QCM.');
                    return false;
                }
            }
            
            // Validation pour réponses multiples : au moins une réponse sélectionnée
            if (type === 'multiple_answer') {
                const selected = document.querySelectorAll('input[name="correct_answers[]"]:checked');
                if (selected.length === 0) {
                    e.preventDefault();
                    alert('Veuillez sélectionner au moins une bonne réponse pour cette question à réponses multiples.');
                    return false;
                }
            }
        }
    });
</script>

<style>
.card-select .form-check-input {
    position: absolute;
    opacity: 0;
}

.card-select .form-check-label .card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.card-select .form-check-input:checked + .form-check-label .card {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.option-item {
    transition: all 0.2s ease;
}

.option-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>
@endpush
@endsection