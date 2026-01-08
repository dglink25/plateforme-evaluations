@extends('layouts.app')

@section('title', 'Résultats : ' . $quiz->title)

@section('header', 'Résultats de l\'évaluation')
@section('subheader', $quiz->title)

@section('content')
<div class="container py-4">
    <!-- Navigation -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('participant.quizzes.index') }}">
            <i class="bi bi-arrow-left me-2"></i>Retour aux quizzes
        </a>
        @if($quiz->user_id === auth()->id())
            <a class="nav-link" href="{{ route('quizzes.participations', $quiz) }}">
                <i class="bi bi-people me-2"></i>Toutes les participations
            </a>
        @endif
    </nav>

    <!-- Résumé du résultat -->
    <div class="row g-4 mb-4">
        <!-- Carte principale -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- En-tête -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="h3 mb-2">{{ $quiz->title }}</h2>
                            <div class="d-flex align-items-center gap-3 text-muted">
                                <span>
                                    <i class="bi bi-clock me-1"></i>Durée : {{ $quiz->duration }} min
                                </span>
                                <span>
                                    <i class="bi bi-question-circle me-1"></i>{{ $questions->count() }} questions
                                </span>
                            </div>
                        </div>
                        
                        <!-- Score global -->
                        <div class="text-center">
                            <div class="display-4 fw-bold text-primary mb-1">
                                @if($participation->score !== null)
                                    {{ $participation->score }}%
                                @else
                                    --
                                @endif
                            </div>
                            <div class="text-muted">Score final</div>
                        </div>
                    </div>
                    
                    <!-- Informations de la participation -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Date de début</div>
                                <div class="fw-medium">
                                    {{ $participation->started_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Date de fin</div>
                                <div class="fw-medium">
                                    {{ optional($participation->completed_at)->format('d/m/Y H:i') ?? '--' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Statut</div>
                                <div class="fw-medium">
                                    @if($participation->status === 'completed')
                                        <span class="text-success">Terminé</span>
                                    @elseif($participation->status === 'graded')
                                        <span class="text-primary">Noté</span>
                                    @else
                                        <span class="text-warning">En cours</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <div class="text-muted small mb-1">Temps écoulé</div>
                                <div class="fw-medium">
                                    @if($participation->completed_at)
                                        {{ $participation->started_at->diffInMinutes($participation->completed_at) }} min
                                    @else
                                        --
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barre de progression -->
                    @php
                        $totalPoints = $questions->sum('points');
                        $userScore = $participation->score ?? 0;
                        $percentage = $totalPoints > 0 ? round(($userScore / $totalPoints) * 100) : 0;
                        
                        // Déterminer la couleur selon le score
                        if ($percentage >= 80) {
                            $scoreColor = 'success';
                        } elseif ($percentage >= 50) {
                            $scoreColor = 'warning';
                        } else {
                            $scoreColor = 'danger';
                        }
                    @endphp
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-medium">Progression du score</span>
                            <span class="fw-bold text-{{ $scoreColor }}">
                                {{ $userScore }} / {{ $totalPoints }} points ({{ $percentage }}%)
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $scoreColor }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%"
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Commentaire du correcteur -->
                    @if($participation->status === 'graded')
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="bi bi-chat-left-text fs-4 me-3"></i>
                                <div>
                                    <h5 class="alert-heading">Retour du correcteur</h5>
                                    @if($participation->answers()->whereNotNull('feedback')->count() > 0)
                                        <p class="mb-0">
                                            Votre évaluation a été corrigée avec des commentaires détaillés. 
                                            Consultez les commentaires pour chaque question ci-dessous.
                                        </p>
                                    @else
                                        <p class="mb-0">
                                            Votre évaluation a été corrigée. Votre score final est de {{ $percentage }}%.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar avec statistiques -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques</h5>
                    
                    @php
                        $correctAnswers = $participation->answers()->where('score', '>', 0)->count();
                        $totalAnswered = $participation->answers()->count();
                        $correctPercentage = $totalAnswered > 0 ? round(($correctAnswers / $totalAnswered) * 100) : 0;
                        
                        $questionsNeedingCorrection = $questions->filter(function($question) use ($participation) {
                            $answer = $question->answers->where('participation_id', $participation->id)->first();
                            return $answer && $answer->score === null && in_array($question->type, ['text', 'file']);
                        })->count();
                    @endphp
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Réponses correctes</span>
                            <span class="fw-bold">{{ $correctAnswers }}/{{ $totalAnswered }}</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Taux de réussite</span>
                            <span class="fw-bold text-{{ $scoreColor }}">{{ $correctPercentage }}%</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Questions à corriger</span>
                            <span class="fw-bold">{{ $questionsNeedingCorrection }}</span>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Score moyen par question</span>
                            <span class="fw-bold">
                                @if($totalAnswered > 0)
                                    {{ round($userScore / $totalAnswered, 1) }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-grid gap-2">
                            <a href="{{ route('participant.quizzes.show', $quiz) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-info-circle me-2"></i>Détails du quiz
                            </a>
                            
                            @if($quiz->user_id === auth()->id())
                                <a href="{{ route('quizzes.participations', $quiz) }}" 
                                   class="btn btn-primary">
                                    <i class="bi bi-people me-2"></i>Toutes les participations
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Détails des réponses -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h3 class="h4 mb-4">Détail des réponses</h3>
            
            @if($questions->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-clipboard display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucune question</h5>
                    <p class="text-muted">Ce quiz ne contient aucune question.</p>
                </div>
            @else
                <div class="accordion" id="answersAccordion">
                    @foreach($questions as $index => $question)
                        @php
                            $userAnswer = $question->answers->where('participation_id', $participation->id)->first();
                            $isCorrect = $userAnswer && $userAnswer->score > 0;
                            $hasFeedback = $userAnswer && $userAnswer->feedback;
                        @endphp
                        
                        <div class="accordion-item mb-3 border rounded">
                            <!-- En-tête de l'accordéon -->
                            <h2 class="accordion-header" id="heading-{{ $question->id }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-{{ $question->id }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse-{{ $question->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-secondary' }} rounded-circle d-flex align-items-center justify-content-center" 
                                                      style="width: 32px; height: 32px;">
                                                    {{ $index + 1 }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $question->question_text }}</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $question->points }} point(s)
                                                    </span>
                                                    <span class="badge bg-info">
                                                        {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="h4 mb-0 {{ $isCorrect ? 'text-success' : 'text-muted' }}">
                                                @if($userAnswer && $userAnswer->score !== null)
                                                    {{ $userAnswer->score }}/{{ $question->points }}
                                                @else
                                                    --/{{ $question->points }}
                                                @endif
                                            </div>
                                            <small class="text-muted">Score</small>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            
                            <!-- Corps de l'accordéon -->
                            <div id="collapse-{{ $question->id }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                 aria-labelledby="heading-{{ $question->id }}"
                                 data-bs-parent="#answersAccordion">
                                <div class="accordion-body">
                                    <!-- Options pour QCM et réponses multiples -->
                                    @if(in_array($question->type, ['multiple_choice', 'multiple_answer']) && $question->options)
                                        <div class="mb-4">
                                            <h6 class="mb-3">Options :</h6>
                                            <div class="list-group">
                                                @foreach($question->options as $optionIndex => $option)
                                                    @php
                                                        $isSelected = false;
                                                        $isCorrectAnswer = false;
                                                        
                                                        if ($question->type === 'multiple_choice') {
                                                            $isSelected = $userAnswer && $userAnswer->answer_content == $optionIndex;
                                                            $isCorrectAnswer = $question->correct_answer == $optionIndex;
                                                        } else {
                                                            $selectedAnswers = $userAnswer ? (array)$userAnswer->answer_content : [];
                                                            $isSelected = in_array($optionIndex, $selectedAnswers);
                                                            $isCorrectAnswer = in_array($optionIndex, (array)$question->correct_answer);
                                                        }
                                                        
                                                        $itemClass = 'list-group-item ';
                                                        if ($isCorrectAnswer && $isSelected) {
                                                            $itemClass .= 'list-group-item-success';
                                                        } elseif ($isCorrectAnswer) {
                                                            $itemClass .= 'list-group-item-info';
                                                        } elseif ($isSelected) {
                                                            $itemClass .= 'list-group-item-danger';
                                                        }
                                                    @endphp
                                                    
                                                    <div class="{{ $itemClass }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                @if($isCorrectAnswer && $isSelected)
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                @elseif($isCorrectAnswer)
                                                                    <i class="bi bi-check-circle text-info"></i>
                                                                @elseif($isSelected)
                                                                    <i class="bi bi-x-circle text-danger"></i>
                                                                @else
                                                                    <i class="bi bi-circle text-muted"></i>
                                                                @endif
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <span class="fw-bold me-2">{{ chr(65 + $optionIndex) }}.</span>
                                                                {{ $option }}
                                                            </div>
                                                            @if($isSelected)
                                                                <span class="badge bg-primary">Votre choix</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Réponse texte -->
                                    @if($question->type === 'text')
                                        <div class="mb-4">
                                            <h6 class="mb-3">Votre réponse :</h6>
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    @if($userAnswer && $userAnswer->answer_content)
                                                        <p class="mb-0">{{ $userAnswer->answer_content }}</p>
                                                    @else
                                                        <p class="mb-0 text-muted fst-italic">Aucune réponse fournie</p>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Réponse modèle -->
                                            @if($quiz->user_id === auth()->id() && $question->correct_answer && isset($question->correct_answer['text']))
                                                <div class="mt-3">
                                                    <h6 class="mb-3">Réponse modèle :</h6>
                                                    <div class="card bg-info bg-opacity-10 border-info">
                                                        <div class="card-body">
                                                            <p class="mb-0">{{ $question->correct_answer['text'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Fichier uploadé -->
                                    @if($question->type === 'file')
                                        <div class="mb-4">
                                            <h6 class="mb-3">Fichier soumis :</h6>
                                            @if($userAnswer && $userAnswer->file_path)
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <i class="bi bi-file-earmark-text fs-2 text-primary"></i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-1">Fichier téléchargé</h6>
                                                                <p class="text-muted mb-0 small">
                                                                    {{ basename($userAnswer->file_path) }}
                                                                </p>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <a href="{{ Storage::url($userAnswer->file_path) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-download me-1"></i>Télécharger
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Aucun fichier téléchargé
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <!-- Commentaire du correcteur -->
                                    @if($hasFeedback)
                                        <div class="alert alert-info">
                                            <div class="d-flex">
                                                <i class="bi bi-chat-left-text fs-4 me-3"></i>
                                                <div>
                                                    <h6 class="alert-heading">Commentaire du correcteur</h6>
                                                    <p class="mb-0">{{ $userAnswer->feedback }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($quiz->user_id === auth()->id() && in_array($question->type, ['text', 'file']) && (!$userAnswer || $userAnswer->score === null))
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning bg-opacity-10">
                                                <h6 class="mb-0">Correction manuelle requise</h6>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('quizzes.answers.update', [$quiz, $participation, $question]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Score (0 à {{ $question->points }})</label>
                                                            <input type="number" 
                                                                   name="score" 
                                                                   min="0" 
                                                                   max="{{ $question->points }}"
                                                                   class="form-control"
                                                                   value="{{ old('score', $userAnswer->score ?? 0) }}"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label">Commentaire</label>
                                                            <textarea name="feedback" 
                                                                      rows="3" 
                                                                      class="form-control"
                                                                      placeholder="Ajoutez un commentaire pour l'apprenant...">{{ old('feedback', $userAnswer->feedback ?? '') }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-end">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-check-circle me-2"></i>
                                                                    Enregistrer la correction
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.accordion-button {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0c63e4;
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.list-group-item-success {
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.list-group-item-info {
    background-color: #cff4fc;
    border-color: #b6effb;
}

.list-group-item-danger {
    background-color: #f8d7da;
    border-color: #f5c2c7;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ouvrir automatiquement la première question
    const firstAccordion = document.querySelector('.accordion-button');
    if (firstAccordion) {
        firstAccordion.click();
    }
    
    // Animation pour la barre de progression
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>
@endpush
@endsection