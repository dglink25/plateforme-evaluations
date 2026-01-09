@extends('layouts.app')

@section('title', 'Participants - ' . $quiz->title)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">{{ $quiz->title }}</h2>
            <p class="text-muted mb-0">Liste des participants et leurs réponses</p>
        </div>
        <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiques</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Participants:</span>
                        <strong>{{ $participations->total() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Corrigés:</span>
                        <strong>{{ $quiz->participations()->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>En attente:</span>
                        <strong>{{ $quiz->participations()->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    @if($participations->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun participant</h5>
                            <p class="text-muted">Aucun utilisateur n'a encore passé ce quiz.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Participant</th>
                                        <th>Date</th>
                                        <th>Réponses</th>
                                        <th>Score</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participations as $participation)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $participation->user->name }}</div>
                                                <div class="small text-muted">{{ $participation->user->email }}</div>
                                            </td>
                                            <td>{{ $participation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $participation->answers_count ?? 0 }} / {{ $quiz->questions_count }}</td>
                                            <td>
                                                <span class="badge bg-{{ $participation->score >= 50 ? 'success' : ($participation->score >= 30 ? 'warning' : 'danger') }}">
                                                    {{ $participation->score ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $statusColors[$participation->status] ?? 'bg-secondary' }}">
                                                    @if($participation->status === 'in_progress')
                                                        En cours
                                                    @elseif($participation->status === 'completed')
                                                        Terminé
                                                    @elseif($participation->status === 'graded')
                                                        Noté
                                                    @else
                                                        {{ $participation->status }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('quizzes.participation.show', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye me-1"></i>Voir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($participations->hasPages())
                            <div class="mt-4">
                                {{ $participations->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection