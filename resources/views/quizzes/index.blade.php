@extends('layouts.app')

@section('title', 'Mes Évaluations')

@section('header', 'Mes Évaluations')

@section('content')
<div class="container py-4">
    <!-- Navigation des quizzes -->
    <nav class="nav nav-pills nav-fill mb-4 bg-light p-3 rounded">
        <a class="nav-link active" href="{{ route('quizzes.index') }}">
            <i class="bi bi-list-check me-2"></i>Mes Évaluations
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Liste de vos évaluations</h5>
                <a href="{{ route('quizzes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Évaluation
                </a>
            </div>

            @if($quizzes->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune évaluation créée</h5>
                    <p class="text-muted">Commencez par créer votre première évaluation.</p>
                    <a href="{{ route('quizzes.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Créer ma première évaluation
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Durée</th>
                                <th>Questions</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $quiz->title }}</div>
                                        @if($quiz->description)
                                            <div class="text-muted small mt-1">
                                                {{ Str::limit($quiz->description, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $quiz->duration }} min
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $quiz->questions_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">
                                            {{ $quiz->is_published ? 'Publié' : 'Brouillon' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('quizzes.questions', $quiz) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir les questions">
                                                <i class="bi bi-question-circle"></i>
                                            </a>
                                            <a href="{{ route('quizzes.edit', $quiz) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete('{{ route('quizzes.destroy', $quiz) }}', '{{ $quiz->title }}')"
                                                    title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($quizzes->hasPages())
                    <div class="mt-4">
                        {{ $quizzes->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete(url, title) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'évaluation "${title}" ?`)) {
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