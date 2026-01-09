@extends('layouts.app')

@section('title', 'Détails du participant')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Correction de {{ $participation->user->name ?? 'Utilisateur' }}</h2>
            <p class="text-muted mb-0">{{ $quiz->title }} - Score: {{ $participation->score ?? 0 }}%</p>
        </div>
        <div>
            <a href="{{ route('quizzes.participations', $quiz) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
            @if($participation->status !== 'grade')
                <form action="{{ route('quizzes.participation.auto-correct', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                    method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-info" onclick="return confirm('Voulez-vous corriger automatiquement toutes les questions de type QCM et Vrai/Faux ?')">
                        <i class="bi bi-robot me-2"></i>Corriger automatiquement
                    </button>
                </form>
                <form action="{{ route('quizzes.participation.validate', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Valider toutes les corrections
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Informations du participant</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Nom:</strong>
                    <p>{{ $participation->user->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Email:</strong>
                    <p>{{ $participation->user->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Date de passage:</strong>
                    <p>{{ $participation->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Statut:</strong>
                    <p>
                        @php
                            $statusColors = [
                                'in_progress' => 'bg-warning',
                                'completed' => 'bg-info',
                                'corrected' => 'bg-success',
                            ];
                        @endphp
                        <span class="badge {{ $statusColors[$participation->status] ?? 'bg-secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $participation->status)) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Réponses aux questions</h5>
            <div class="small text-muted">
                Score total: <strong>{{ $participation->answers->sum('score') }}</strong> / 
                {{ $quiz->questions->sum('points') }} points
            </div>
        </div>
        <div class="card-body">
            @foreach($questions as $question)
                @php
                    $userAnswer = $answers->where('question_id', $question->id)->first();
                    $isAutoCorrectable = in_array($question->type, ['multiple_choice', 'true_false']);
                    $isCorrect = false;
                    
                    // Vérifier si la réponse est correcte (pour QCM et Vrai/Faux)
                    if ($userAnswer && $isAutoCorrectable) {
                        $userResponse = $userAnswer->answer_content;
                        $correctResponse = $question->correct_answer;
                        
                        if (is_array($userResponse) && is_array($correctResponse)) {
                            sort($userResponse);
                            sort($correctResponse);
                            $isCorrect = $userResponse == $correctResponse;
                        } else {
                            $isCorrect = $userResponse == $correctResponse;
                        }
                    }
                @endphp
                
                <div class="mb-4 p-3 border rounded {{ $isCorrect ? 'border-success bg-success-light' : 'border-secondary' }}">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-0">Question {{ $loop->iteration }}</h6>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $question->type)) }} - {{ $question->points }} points</small>
                        </div>
                        @if($userAnswer && $userAnswer->score !== null)
                            <span class="badge bg-info">{{ $userAnswer->score }} / {{ $question->points }}</span>
                        @endif
                    </div>
                    
                    <p class="fw-semibold">{{ $question->question_text }}</p>
                    
                    <!-- Options de la question (pour QCM) -->
                    @if($question->options && $question->type === 'multiple_choice')
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Options disponibles:</small>
                            @foreach($question->options as $index => $option)
                                @php
                                    $isUserChoice = $userAnswer && 
                                                   (($userAnswer->answer_content == $index) || 
                                                    (is_array($userAnswer->answer_content) && in_array($index, $userAnswer->answer_content)));
                                    $isCorrectOption = is_array($question->correct_answer) 
                                        ? in_array($index, $question->correct_answer)
                                        : $question->correct_answer == $index;
                                @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="{{ is_array($question->correct_answer) ? 'checkbox' : 'radio' }}" 
                                           disabled {{ $isUserChoice ? 'checked' : '' }}>
                                    <label class="form-check-label {{ $isCorrectOption ? 'fw-bold text-success' : '' }}">
                                        {{ $option }}
                                        @if($isCorrectOption)
                                            <small class="text-success">(Correcte)</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Réponse du participant -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Réponse du participant:</small>
                        @if($userAnswer)
                            <div class="alert {{ $isCorrect ? 'alert-success' : ($userAnswer->score ? 'alert-info' : 'alert-danger') }}">
                                @if($question->type === 'multiple_choice')
                                    @if(is_array($userAnswer->answer_content))
                                        <ul class="mb-0">
                                            @foreach($userAnswer->answer_content as $choiceIndex)
                                                @if(isset($question->options[$choiceIndex]))
                                                    <li>{{ $question->options[$choiceIndex] }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @elseif(isset($question->options[$userAnswer->answer_content]))
                                        <strong>{{ $question->options[$userAnswer->answer_content] }}</strong>
                                    @endif
                                @elseif($question->type === 'true_false')
                                    <strong>{{ $userAnswer->answer_content ? 'Vrai' : 'Faux' }}</strong>
                                    @if($question->correct_answer !== null)
                                        <div class="mt-1">
                                            <small>La réponse correcte était: <strong>{{ $question->correct_answer ? 'Vrai' : 'Faux' }}</strong></small>
                                        </div>
                                    @endif
                                @else
                                    <strong>{{ $userAnswer->answer_content ?? 'Aucune réponse' }}</strong>
                                @endif
                                
                                @if($userAnswer->file_path)
                                    <div class="mt-2">
                                        <strong>Fichier joint:</strong>
                                        <a href="{{ Storage::url($userAnswer->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i>Télécharger
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            @if($userAnswer->feedback)
                                <div class="alert alert-warning">
                                    <strong>Feedback:</strong> {{ $userAnswer->feedback }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                <strong>Aucune réponse donnée</strong>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Correction manuelle -->
                    @if($userAnswer && (!$isAutoCorrectable || $userAnswer->score === null))
                        <div class="mt-3 p-3 bg-light rounded">
                            <form action="{{ route('quizzes.participation.correct', ['quiz' => $quiz, 'participation' => $participation]) }}" 
                                  method="POST">
                                @csrf
                                <input type="hidden" name="answer_id" value="{{ $userAnswer->id }}">
                                
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label for="score_{{ $question->id }}" class="form-label">
                                            Note (sur {{ $question->points }} points)
                                        </label>
                                        <input type="number" 
                                               step="0.5"
                                               class="form-control" 
                                               id="score_{{ $question->id }}" 
                                               name="score" 
                                               min="0" 
                                               max="{{ $question->points }}" 
                                               value="{{ $userAnswer->score ?? 0 }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="feedback_{{ $question->id }}" class="form-label">
                                            Feedback (optionnel)
                                        </label>
                                        <textarea class="form-control" 
                                                  id="feedback_{{ $question->id }}" 
                                                  name="feedback" 
                                                  rows="2">{{ $userAnswer->feedback ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>Noter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.bg-success-light {
    background-color: rgba(25, 135, 84, 0.1);
}
</style>

<script>
function autoCorrectAll() {
    if (confirm('Voulez-vous corriger automatiquement toutes les questions de type QCM et Vrai/Faux ?')) {
        window.location.href = "{{ route('quizzes.participation.auto-correct', ['quiz' => $quiz, 'participation' => $participation]) }}";
    }
}
</script>
@endsection