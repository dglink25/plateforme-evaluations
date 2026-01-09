<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Participation;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Afficher la liste des quizzes
     */
    public function index()
    {
        $quizzes = Quiz::where('user_id', auth()->id())
                      ->withCount(['questions', 'participations'])
                      ->latest()
                      ->paginate(10);
        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('quizzes.create');
    }

    /**
     * Enregistrer un nouveau quiz
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'required|boolean',
            'duration' => 'required|integer|min:1',
        ]);

        $validated['user_id'] = auth()->id();
        
        Quiz::create($validated);

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz créé avec succès.');
    }

    /**
     * Afficher un quiz spécifique
     */
    public function show(Quiz $quiz)
    {
       // $this->authorize('view', $quiz);
        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Quiz $quiz)
    {
       // $this->authorize('update', $quiz);
        return view('quizzes.edit', compact('quiz'));
    }

    /**
     * Mettre à jour un quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
       // $this->authorize('update', $quiz);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $quiz->update($validated);

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz mis à jour avec succès.');
    }

    /**
     * Supprimer un quiz
     */
    public function destroy(Quiz $quiz)
    {
       // $this->authorize('delete', $quiz);
        $quiz->delete();

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz supprimé avec succès.');
    }

    /**
     * Afficher les questions d'un quiz
     */
    public function questions(Quiz $quiz)
    {
        // $this->authorize('update', $quiz);
        $questions = $quiz->questions;
        return view('quizzes.questions', compact('quiz', 'questions'));
    }

    /**
     * Afficher les participations à un quiz (pour les corrections)
     */
    public function participations(Quiz $quiz)
    {
       // $this->authorize('view', $quiz);
        $participations = $quiz->participations()
            ->with('user')
            ->withCount('answers')
            ->latest()
            ->paginate(10);
        
        return view('quizzes.participations', compact('quiz', 'participations'));
    }

    /**
     * Afficher les détails d'une participation (réponses)
     */
    public function showParticipation(Quiz $quiz, Participation $participation)
    {
        // $this->authorize('view', $quiz);
        
        // Charger les questions du quiz
        $questions = $quiz->questions;
        
        // Charger les réponses de cette participation
        $answers = $participation->answers;
        
        return view('quizzes.participation-details', compact(
            'quiz', 
            'participation', 
            'questions', 
            'answers'
        ));
    }

    /**
     * Corriger manuellement une réponse
     */
    public function correctAnswer(Request $request, Quiz $quiz, Participation $participation)
    {
       // $this->authorize('update', $quiz);
        
        $request->validate([
            'answer_id' => 'required|exists:answers,id',
            'score' => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:500',
        ]);
        
        $answer = Answer::findOrFail($request->answer_id);
        
        // Vérifier que la réponse appartient bien à cette participation
        if ($answer->participation_id !== $participation->id) {
            return redirect()->back()->with('error', 'Réponse invalide.');
        }
        
        // Mettre à jour le score et le feedback
        $answer->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
        ]);
        
        // Recalculer le score total de la participation
        $this->calculateParticipationScore($participation);
        
        return redirect()->back()->with('success', 'Réponse corrigée avec succès.');
    }

    /**
     * Valider toutes les corrections d'une participation
     */
    public function validateCorrections(Quiz $quiz, Participation $participation)
    {
       // $this->authorize('update', $quiz);
        
        // Marquer la participation comme corrigée
        $participation->update(['status' => 'graded']);
        
        return redirect()->route('quizzes.participations', $quiz)
            ->with('success', 'Corrections validées avec succès.');
    }

    /**
     * Corriger automatiquement toutes les réponses d'une participation
     */
    public function autoCorrectAll(Quiz $quiz, Participation $participation)
    {
       // $this->authorize('update', $quiz);
        
        $questions = $quiz->questions;
        $answers = $participation->answers;
        
        foreach ($questions as $question) {
            $userAnswer = $answers->where('question_id', $question->id)->first();
            
            if ($userAnswer && !$userAnswer->score) {
                $score = $this->calculateAutoScore($question, $userAnswer);
                
                $userAnswer->update([
                    'score' => $score,
                    'feedback' => $score > 0 ? 'Corrigé automatiquement' : 'Réponse incorrecte',
                ]);
            }
        }
        
        // Recalculer le score total
        $this->calculateParticipationScore($participation);
        
        return redirect()->back()->with('success', 'Correction automatique terminée.');
    }

    /**
     * Calculer le score automatiquement pour une réponse
     */
    private function calculateAutoScore(Question $question, Answer $answer)
    {
        switch ($question->type) {
            case 'multiple_choice':
                // Pour QCM, comparer les réponses
                $userAnswer = $answer->answer_content;
                $correctAnswer = $question->correct_answer;
                
                // Si c'est un tableau, comparer les valeurs
                if (is_array($userAnswer) && is_array($correctAnswer)) {
                    sort($userAnswer);
                    sort($correctAnswer);
                    return $userAnswer == $correctAnswer ? $question->points : 0;
                }
                
                // Sinon, comparer directement
                return $userAnswer == $correctAnswer ? $question->points : 0;
                
            case 'true_false':
                // Vrai/Faux - simple comparaison
                return $answer->answer_content == $question->correct_answer ? $question->points : 0;
                
            case 'short_answer':
                // Réponse courte - peut nécessiter une correction manuelle
                return 0;
                
            case 'essay':
                // Dissertation - nécessite une correction manuelle
                return 0;
                
            default:
                return 0;
        }
    }

    /**
     * Recalculer le score total d'une participation
     */
    private function calculateParticipationScore(Participation $participation)
    {
        $totalScore = $participation->answers()->sum('score');
        $maxPossibleScore = $participation->quiz->questions()->sum('points');
        
        $finalScore = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;
        
        $participation->update([
            'score' => round($finalScore, 2),
        ]);
        
        return $finalScore;
    }
}