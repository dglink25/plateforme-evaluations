@extends('layouts.app')

@section('title', $quiz->title)

@section('header', $quiz->title)

@section('content')
<div class="container py-4">
    <!-- Navigation -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('participant.quizzes.index') }}">
            <i class="bi bi-arrow-left me-2"></i>Retour aux quizzes
        </a>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-x-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- En-tête du quiz -->
            <div class="mb-5">
                <h2 class="card-title mb-3">{{ $quiz->title }}</h2>
                
                @if($quiz->description)
                    <div class="mb-4">
                        <p class="text-muted">{{ $quiz->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Informations du quiz -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card bg-primary bg-opacity-10 border-primary border">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-3 me-3">
                                    <i class="bi bi-clock fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="text-primary mb-1">Durée</h6>
                                    <h4 class="fw-bold">{{ $quiz->duration }} min</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-purple bg-opacity-10 border-purple border">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="bg-purple text-white rounded-circle p-3 me-3">
                                    <i class="bi bi-question-circle fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="text-purple mb-1">Questions</h6>
                                    <h4 class="fw-bold">{{ $quiz->questions->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card {{ $participation ? 'bg-success bg-opacity-10 border-success' : 'bg-info bg-opacity-10 border-info' }} border">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="{{ $participation ? 'bg-success' : 'bg-info' }} text-white rounded-circle p-3 me-3">
                                    <i class="bi bi-{{ $participation ? 'check-circle' : 'play-circle' }} fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="{{ $participation ? 'text-success' : 'text-info' }} mb-1">Statut</h6>
                                    <h4 class="fw-bold">
                                        @if($participation)
                                            @if($participation->status === 'completed' || $participation->status === 'graded')
                                                Terminé
                                            @else
                                                En cours
                                            @endif
                                        @else
                                            Disponible
                                        @endif
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="alert alert-warning mb-5">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading">Instructions importantes</h5>
                        <ul class="mb-0">
                            <li>Le temps commence dès que vous cliquez sur "Commencer le Quiz"</li>
                            <li>Vous ne pourrez pas mettre en pause le chronomètre</li>
                            <li>Assurez-vous d'avoir une connexion Internet stable</li>
                            <li>Les réponses sont enregistrées automatiquement</li>
                            <li>Le quiz se soumet automatiquement à la fin du temps</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bouton d'action -->
            <div class="text-center mb-4">
                @if($participation)
                    @if($participation->status === 'in_progress')
                        <a href="{{ route('participant.quizzes.take', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                           class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-play-circle me-2"></i>
                            Reprendre le Quiz
                        </a>
                    @else
                        <a href="{{ route('participant.quizzes.result', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                           class="btn btn-success btn-lg px-5">
                            <i class="bi bi-bar-chart me-2"></i>
                            Voir mes résultats
                        </a>
                    @endif
                @else
                    <form action="{{ route('participant.quizzes.start', $quiz) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="btn btn-primary btn-lg px-5"
                                onclick="return confirm('Êtes-vous prêt à commencer ? Le chronomètre démarrera immédiatement.')">
                            <i class="bi bi-play-circle me-2"></i>
                            Commencer le Quiz
                        </button>
                    </form>
                @endif
            </div>

            <!-- Retour -->
            <div class="text-center">
                <a href="{{ route('participant.quizzes.index') }}" 
                   class="text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Retour à la liste des quizzes
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.border-purple {
    border-color: #6f42c1 !important;
}
</style>
@endsection