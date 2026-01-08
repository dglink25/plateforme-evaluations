<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    /**
     * Afficher le formulaire de création de question
     */
    public function create(Quiz $quiz)
    {
        Gate::authorize('update', $quiz);
        return view('questions.create', compact('quiz'));
    }

    /**
     * Enregistrer une nouvelle question
     */
    public function store(Request $request, Quiz $quiz)
    {
        Gate::authorize('update', $quiz);
        
        $validated = $this->validateQuestion($request);
        
        $question = $quiz->questions()->create($validated);
        
        return redirect()->route('quizzes.questions', $quiz)
            ->with('success', 'Question ajoutée avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Quiz $quiz, Question $question)
    {
        Gate::authorize('update', $quiz);
        
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        return view('questions.edit', compact('quiz', 'question'));
    }

    /**
     * Mettre à jour une question
     */
    public function update(Request $request, Quiz $quiz, Question $question)
    {
        Gate::authorize('update', $quiz);
        
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        $validated = $this->validateQuestion($request);
        
        $question->update($validated);
        
        return redirect()->route('quizzes.questions', $quiz)
            ->with('success', 'Question mise à jour avec succès.');
    }

    /**
     * Supprimer une question
     */
    public function destroy(Quiz $quiz, Question $question)
    {
        Gate::authorize('update', $quiz);
        
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        $question->delete();
        
        return redirect()->route('quizzes.questions', $quiz)
            ->with('success', 'Question supprimée avec succès.');
    }

    /**
     * Validation des données de la question
     */
    private function validateQuestion(Request $request)
    {
        $rules = [
            'question_text' => 'required|string|min:3',
            'type' => 'required|in:multiple_choice,multiple_answer,text,file',
            'points' => 'required|integer|min:1|max:100',
        ];

        $data = $request->validate($rules);

        // Traitement selon le type de question
        switch ($data['type']) {
            case 'multiple_choice':
                $data['options'] = $request->input('options', []);
                $data['correct_answer'] = (int) $request->input('correct_answer');
                break;
                
            case 'multiple_answer':
                $data['options'] = $request->input('options', []);
                $data['correct_answer'] = $request->input('correct_answers', []);
                break;
                
            case 'text':
                $data['options'] = null;
                $data['correct_answer'] = ['text' => $request->input('correct_answer_text', '')];
                break;
                
            case 'file':
                $data['options'] = null;
                $data['correct_answer'] = null;
                break;
        }

        return $data;
    }
}