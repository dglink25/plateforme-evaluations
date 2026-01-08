<?php

namespace App\Http\Controllers;

use App\Models\Participation;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller{
    /**
     * Afficher la liste des quizzes disponibles
     */
    public function index() {
        $quizzes = Quiz::with(['user', 'questions'])
            ->latest()
            ->paginate(12);

        $userParticipations = Participation::where('user_id', Auth::id())
            ->whereIn('status', ['completed', 'graded'])
            ->pluck('quiz_id')
            ->toArray();

        return view('participant.quizzes.index', compact('quizzes', 'userParticipations'));
    }

    /**
     * Afficher les détails d'un quiz avant de commencer
     */
    public function show(Quiz $quiz)
    {

        // Vérifier si l'utilisateur a déjà participé
        $participation = $quiz->getUserParticipation(Auth::id());
        
        return view('participant.quizzes.show', compact('quiz', 'participation'));
    }

    /**
     * Commencer un quiz
     */
    public function start(Quiz $quiz)
    {
        // Vérifier si le quiz est publié
        if (!$quiz->is_published) {
            abort(403, 'Ce quiz n\'est pas encore publié.');
        }

        // Vérifier si l'utilisateur a déjà une participation en cours
        $existingParticipation = $quiz->getUserParticipation(Auth::id());
        
        if ($existingParticipation) {
            // Rediriger vers la participation existante
            return redirect()->route('participant.quizzes.take', [
                'quiz' => $quiz,
                'participation' => $existingParticipation
            ]);
        }

        // Créer une nouvelle participation
        $participation = Participation::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'started_at' => Carbon::now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('participant.quizzes.take', [
            'quiz' => $quiz,
            'participation' => $participation
        ]);
    }

    /**
     * Afficher le quiz avec les questions
     */
    public function take(Quiz $quiz, Participation $participation)
    {
        // Vérifier les autorisations
        if ($participation->user_id !== Auth::id() || $participation->quiz_id !== $quiz->id) {
            abort(403);
        }

        // Vérifier si le quiz est encore en cours
        if ($participation->status !== 'in_progress') {
            return redirect()->route('participant.quizzes.show', $quiz)
                ->with('error', 'Ce quiz a déjà été terminé.');
        }

        // Calculer le temps restant
        $startTime = $participation->started_at;
        $duration = $quiz->duration; // en minutes
        $endTime = $startTime->copy()->addMinutes($duration);
        $now = Carbon::now();
        
        $remainingSeconds = $now->greaterThan($endTime) ? 0 : $now->diffInSeconds($endTime);

        // Récupérer les questions avec les options
        $questions = $quiz->questions()->with('answers', function ($query) use ($participation) {
            $query->where('participation_id', $participation->id);
        })->get();

        return view('participant.quizzes.take', compact('quiz', 'participation', 'questions', 'remainingSeconds'));
    }

    /**
     * Soumettre les réponses
     */
    public function submit(Request $request, Quiz $quiz, Participation $participation)
    {
        // Vérifier les autorisations
        if ($participation->user_id !== Auth::id() || $participation->quiz_id !== $quiz->id) {
            abort(403);
        }

        // Vérifier si le quiz est encore en cours
        if ($participation->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Ce quiz a déjà été terminé.'
            ], 400);
        }

        // Vérifier si le temps est écoulé
        $startTime = $participation->started_at;
        $duration = $quiz->duration;
        $endTime = $startTime->copy()->addMinutes($duration);
        
        if (Carbon::now()->greaterThan($endTime)) {
            $participation->update([
                'status' => 'completed',
                'completed_at' => Carbon::now()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Le temps est écoulé. Le quiz a été soumis automatiquement.'
            ], 400);
        }

        try {
            // Traiter les réponses
            $answers = $request->input('answers', []);
            $files = $request->file('files', []);
            
            foreach ($answers as $questionId => $answer) {
                $question = Question::find($questionId);
                
                if ($question && $question->quiz_id === $quiz->id) {
                    // Vérifier si une réponse existe déjà
                    $existingAnswer = $participation->answers()->where('question_id', $questionId)->first();
                    
                    if ($existingAnswer) {
                        // Mettre à jour la réponse existante
                        $existingAnswer->update([
                            'answer_content' => $this->formatAnswerContent($question->type, $answer),
                        ]);
                    } else {
                        // Créer une nouvelle réponse
                        $participation->answers()->create([
                            'question_id' => $questionId,
                            'answer_content' => $this->formatAnswerContent($question->type, $answer),
                        ]);
                    }
                }
            }

            // Gérer les fichiers uploadés
            foreach ($files as $questionId => $file) {
                $question = Question::find($questionId);
                
                if ($question && $question->quiz_id === $quiz->id && $question->type === 'file' && $file) {
                    $path = $file->store('quiz_answers', 'public');
                    
                    $existingAnswer = $participation->answers()->where('question_id', $questionId)->first();
                    
                    if ($existingAnswer) {
                        // Supprimer l'ancien fichier si existe
                        if ($existingAnswer->file_path && \Storage::disk('public')->exists($existingAnswer->file_path)) {
                            \Storage::disk('public')->delete($existingAnswer->file_path);
                        }
                        
                        $existingAnswer->update([
                            'file_path' => $path,
                        ]);
                    } else {
                        $participation->answers()->create([
                            'question_id' => $questionId,
                            'file_path' => $path,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Réponses sauvegardées avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminer le quiz
     */
    public function finish(Request $request, Quiz $quiz, Participation $participation)
    {
        // Vérifier les autorisations
        if ($participation->user_id !== Auth::id() || $participation->quiz_id !== $quiz->id) {
            abort(403);
        }

        // Vérifier si le quiz est encore en cours
        if ($participation->status !== 'in_progress') {
            return redirect()->route('participant.quizzes.show', $quiz)
                ->with('error', 'Ce quiz a déjà été terminé.');
        }

        // Sauvegarder les réponses une dernière fois
        $this->submit($request, $quiz, $participation);

        // Mettre à jour la participation
        $participation->update([
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ]);

        // Correction automatique pour les QCM
        $this->autoGrade($participation);

        return redirect()->route('participant.quizzes.result', [$quiz, $participation])
            ->with('success', 'Quiz terminé avec succès !');
    }

    /**
     * Afficher les résultats
     */
    public function result(Quiz $quiz, Participation $participation)
    {
        // Vérifier les autorisations
        if ($participation->user_id !== Auth::id() || $participation->quiz_id !== $quiz->id) {
            abort(403);
        }

        // Vérifier si le quiz est terminé
        if ($participation->status !== 'completed' && $participation->status !== 'graded') {
            return redirect()->route('participant.quizzes.take', [$quiz, $participation]);
        }

        // Charger les questions avec les réponses
        $questions = $quiz->questions()->with(['answers' => function ($query) use ($participation) {
            $query->where('participation_id', $participation->id);
        }])->get();

        return view('participant.quizzes.result', compact('quiz', 'participation', 'questions'));
    }

    /**
     * Formater le contenu de la réponse selon le type
     */
    private function formatAnswerContent($type, $answer)
    {
        switch ($type) {
            case 'multiple_choice':
                return (int) $answer;
            case 'multiple_answer':
                return array_map('intval', (array) $answer);
            case 'text':
                return (string) $answer;
            default:
                return $answer;
        }
    }

    /**
     * Correction automatique pour les QCM
     */
    private function autoGrade(Participation $participation)
    {
        $totalScore = 0;
        $answers = $participation->answers()->with('question')->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            
            if ($question->type === 'multiple_choice') {
                if ($answer->answer_content == $question->correct_answer) {
                    $answer->update(['score' => $question->points]);
                    $totalScore += $question->points;
                }
            } elseif ($question->type === 'multiple_answer') {
                $userAnswers = (array) $answer->answer_content;
                $correctAnswers = (array) $question->correct_answer;
                
                sort($userAnswers);
                sort($correctAnswers);
                
                if ($userAnswers == $correctAnswers) {
                    $answer->update(['score' => $question->points]);
                    $totalScore += $question->points;
                }
            }
        }

        // Mettre à jour le score total
        $participation->update(['score' => $totalScore]);
    }
}