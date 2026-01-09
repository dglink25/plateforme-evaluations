<h6 class="fw-bold mb-3 d-flex align-items-center">
    <i class="bi bi-graph-up me-2"></i>Progression
</h6>

<!-- Barre de progression -->
<div class="mb-4">
    <div class="d-flex justify-content-between mb-2">
        <small class="text-muted">Avancement</small>
        <small id="answered-count" class="fw-bold">0</small>
    </div>
    <div class="progress" style="height: 10px; border-radius: 5px;">
        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: 0%">
        </div>
    </div>
    <small class="text-muted d-block mt-1">
        <span id="answered-count-text">0</span>/{{ $questions->count() }} questions
    </small>
</div>

<!-- Navigation des questions -->
<h6 class="fw-bold mb-3 d-flex align-items-center">
    <i class="bi bi-compass me-2"></i>Navigation
</h6>
<div class="mb-4">
    <div class="row g-2" id="question-nav-grid">
        @foreach($questions as $index => $question)
            <div class="col-3 col-md-2">
                <a href="#question-{{ $question->id }}" 
                   class="question-nav d-block text-center p-2 border rounded"
                   data-question-id="{{ $question->id }}"
                   title="Question {{ $index + 1 }}">
                    <div class="question-number fw-bold">{{ $index + 1 }}</div>
                    <div class="question-status mt-1">
                        <div class="status-indicator" data-question-id="{{ $question->id }}"></div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Informations -->
<div class="mb-4">
    <h6 class="fw-bold mb-3 d-flex align-items-center">
        <i class="bi bi-info-circle me-2"></i>Informations
    </h6>
    <div class="small">
        <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Durée:</span>
            <span class="fw-semibold">{{ $quiz->duration }} min</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Questions:</span>
            <span class="fw-semibold">{{ $questions->count() }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">Sauvegarde:</span>
            <span class="text-success fw-semibold">Auto</span>
        </div>
    </div>
</div>

<!-- Bouton de soumission -->
<button type="button" 
        id="submit-btn-desktop"
        class="btn btn-success w-100 d-flex align-items-center justify-content-center"
        data-bs-toggle="modal" 
        data-bs-target="#submitModal">
    <i class="bi bi-check-circle me-2"></i>
    Soumettre le quiz
</button>

<small class="text-muted d-block mt-2 text-center">
    <i class="bi bi-clock-history me-1"></i>Soumission auto à la fin
</small>