<!-- Barre de progression mobile -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0 fw-bold">Progression</h6>
        <small id="answered-count-mobile" class="fw-bold text-primary">0/{{ $questions->count() }}</small>
    </div>
    <div class="progress" style="height: 8px;">
        <div id="progress-bar-mobile" class="progress-bar" role="progressbar" style="width: 0%"></div>
    </div>
</div>

<!-- Navigation rapide mobile -->
<div class="mb-4">
    <h6 class="fw-bold mb-3">Navigation rapide</h6>
    <div class="row g-2">
        @foreach($questions as $index => $question)
            <div class="col-2">
                <a href="#question-{{ $question->id }}" 
                   class="question-nav d-block text-center p-1 border rounded"
                   data-question-id="{{ $question->id }}"
                   title="Q{{ $index + 1 }}">
                    <div class="question-number small fw-bold">{{ $index + 1 }}</div>
                    <div class="question-status">
                        <div class="status-indicator mx-auto" data-question-id="{{ $question->id }}"></div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Informations mobiles -->
<div class="card bg-light border-0 mb-4">
    <div class="card-body p-3">
        <div class="row text-center">
            <div class="col-6 border-end">
                <div class="text-muted small">Durée</div>
                <div class="fw-bold">{{ $quiz->duration }} min</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Questions</div>
                <div class="fw-bold">{{ $questions->count() }}</div>
            </div>
        </div>
    </div>
</div>