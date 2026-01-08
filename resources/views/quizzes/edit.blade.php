@extends('layouts.app')

@section('title', 'Modifier l\'Évaluation : ' . $quiz->title)

@section('header', 'Modifier l\'Évaluation : ' . $quiz->title)

@section('content')
<div class="container py-4">
    <!-- Navigation des quizzes -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link" href="{{ route('quizzes.index') }}">
            <i class="bi bi-list-check me-2"></i>Mes Évaluations
        </a>
        <a class="nav-link active" href="#">
            <i class="bi bi-pencil me-2"></i>Modifier
        </a>
        <a class="nav-link" href="{{ route('participant.quizzes.index') }}">
            <i class="bi bi-clock me-2"></i>Passer un Quiz
        </a>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Modifier l'évaluation</h5>

                    <form method="POST" action="{{ route('quizzes.update', $quiz) }}">
                        @csrf
                        @method('PUT')

                        <!-- Titre -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">
                                <i class="bi bi-card-heading me-1"></i>Titre de l'évaluation *
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $quiz->title) }}" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i>Description
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3"
                                      class="form-control">{{ old('description', $quiz->description) }}</textarea>
                        </div>

                        <!-- Durée -->
                        <div class="mb-4">
                            <label for="duration" class="form-label fw-semibold">
                                <i class="bi bi-clock me-1"></i>Durée (en minutes) *
                            </label>
                            <input type="number" 
                                   name="duration" 
                                   id="duration" 
                                   min="1" 
                                   max="300"
                                   class="form-control @error('duration') is-invalid @enderror"
                                   value="{{ old('duration', $quiz->duration) }}" 
                                   required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut de publication -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-eye me-1"></i>Statut de publication
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_published" 
                                       id="is_published" 
                                       value="1"
                                       {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_published">
                                    Publier ce quiz (le rendre visible pour les participants)
                                </label>
                            </div>
                            <div class="form-text">
                                Une fois publié, le quiz apparaîtra dans la liste des quizzes disponibles.
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
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
@endsection