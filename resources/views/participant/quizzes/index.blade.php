@extends('layouts.app')

@section('title', 'Quizzes Disponibles')

@section('header', 'Quizzes Disponibles')

@section('content')
<div class="container py-4">
    <!-- Navigation -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('quizzes.index') }}">
            <i class="bi bi-list-check me-2"></i>Mes Évaluations
        </a>
        <a class="nav-link active" href="#">
            <i class="bi bi-clock me-2"></i>Passer un Quiz
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

    @if($quizzes->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-clipboard display-1 text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">Aucun quiz disponible</h4>
            <p class="text-muted mb-4">
                Aucun quiz n'a été publié pour le moment.
            </p>
        </div>
    @else
        <div class="row g-4">
            @foreach($quizzes as $quiz)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm hover-shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">{{ $quiz->title }}</h5>
                                    
                                    @if($quiz->description)
                                        <p class="card-text text-muted small mb-3 line-clamp-2">
                                            {{ $quiz->description }}
                                        </p>
                                    @endif
                                </div>
                                
                                @if(in_array($quiz->id, $userParticipations))
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-lg me-1"></i>Terminé
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-between text-muted small mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock me-1"></i>
                                    <span>{{ $quiz->duration }} minutes</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-question-circle me-1"></i>
                                    <span>{{ $quiz->questions_count ?? $quiz->questions->count() }} questions</span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Par {{ $quiz->user->name }}
                                </div>
                                
                                <a href="{{ route('participant.quizzes.show', $quiz) }}" 
                                   class="btn btn-sm {{ in_array($quiz->id, $userParticipations) ? 'btn-outline-success' : 'btn-primary' }}">
                                    @if(in_array($quiz->id, $userParticipations))
                                        <i class="bi bi-eye me-1"></i>Voir résultat
                                    @else
                                        <i class="bi bi-arrow-right me-1"></i>Détails
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($quizzes->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $quizzes->links() }}
            </div>
        @endif
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
@endsection