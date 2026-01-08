@extends('layouts.app')

@section('title', 'Questions : ' . $quiz->title)

@section('header', 'Questions : ' . $quiz->title)

@section('content')
<div class="container py-4">
    <!-- Navigation des quizzes -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('quizzes.index') }}">
            <i class="bi bi-list-check me-2"></i>Mes Évaluations
        </a>
        <a class="nav-link active" href="#">
            <i class="bi bi-question-circle me-2"></i>Questions
        </a>
        <a class="nav-link" href="{{ route('participant.quizzes.index') }}">
            <i class="bi bi-clock me-2"></i>Passer un Quiz
        </a>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="card-title mb-1">{{ $quiz->title }}</h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-clock me-1"></i>{{ $quiz->duration }} minutes • 
                        <i class="bi bi-question-circle me-1 ms-3"></i>{{ $questions->count() }} questions
                    </p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('questions.create', $quiz) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter une question
                    </a>
                </div>
            </div>

            @if($questions->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-question-circle display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune question</h5>
                    <p class="text-muted mb-4">Commencez par ajouter votre première question.</p>
                    <a href="{{ route('questions.create', $quiz) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter une question
                    </a>
                </div>
            @else
                <!-- Liste des questions -->
                <div class="mb-4">
                    @foreach($questions as $index => $question)
                        <div class="card mb-3 border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-start flex-grow-1">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 36px; height: 36px; min-width: 36px; margin-right: 12px;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-semibold mb-1">{{ $question->question_text }}</h6>
                                            <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                                                <span class="badge 
                                                    @if($question->type === 'multiple_choice') bg-primary
                                                    @elseif($question->type === 'multiple_answer') bg-purple
                                                    @elseif($question->type === 'text') bg-success
                                                    @else bg-warning @endif">
                                                    @switch($question->type)
                                                        @case('multiple_choice') QCM (1 réponse) @break
                                                        @case('multiple_answer') Réponses multiples @break
                                                        @case('text') Réponse texte @break
                                                        @case('file') Fichier @break
                                                    @endswitch
                                                </span>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-star me-1"></i>{{ $question->points }} point(s)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('questions.edit', [$quiz, $question]) }}" 
                                           class="btn btn-sm btn-outline-warning"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete('{{ route('questions.destroy', [$quiz, $question]) }}', 'cette question')"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                @if($question->options && in_array($question->type, ['multiple_choice', 'multiple_answer']))
                                    <div class="mt-3 ms-5">
                                        <p class="fw-semibold text-muted mb-2">Options :</p>
                                        <div class="row g-2">
                                            @foreach($question->options as $optionIndex => $option)
                                                <div class="col-md-6">
                                                    <div class="border rounded p-2 
                                                        @if(($question->type === 'multiple_choice' && $question->correct_answer == $optionIndex) ||
                                                            ($question->type === 'multiple_answer' && in_array($optionIndex, $question->correct_answer)))
                                                            border-success bg-success bg-opacity-10
                                                        @endif">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-secondary me-2">{{ chr(65 + $optionIndex) }}</span>
                                                            <span class="flex-grow-1">{{ $option }}</span>
                                                            @if(($question->type === 'multiple_choice' && $question->correct_answer == $optionIndex) ||
                                                                ($question->type === 'multiple_answer' && in_array($optionIndex, $question->correct_answer)))
                                                                <span class="badge bg-success ms-2">
                                                                    <i class="bi bi-check-lg"></i> Correct
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($question->type === 'text' && isset($question->correct_answer['text']))
                                    <div class="mt-3 ms-5">
                                        <p class="fw-semibold text-muted mb-2">Réponse modèle :</p>
                                        <div class="bg-light border rounded p-3">
                                            <p class="mb-0">{{ $question->correct_answer['text'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Résumé -->
                <div class="alert alert-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            Total des points : <strong>{{ $questions->sum('points') }}</strong>
                        </div>
                        <div>
                            Temps estimé par question : 
                            <strong>{{ round($quiz->duration / max($questions->count(), 1), 1) }} min</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete(url, itemName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer ${itemName} ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection