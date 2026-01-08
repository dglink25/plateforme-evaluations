<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Afficher la liste des quizzes
     */
    public function index()
    {
        $quizzes = Quiz::where('user_id', auth()->id())->latest()->paginate(10);
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
            'is_published'=>'required',
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
    public function show(Quiz $quiz){
        //$this->authorize('view', $quiz);
        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Quiz $quiz)
    {
        //$this->authorize('update', $quiz);
        return view('quizzes.edit', compact('quiz'));
    }

    /**
     * Mettre à jour un quiz
     */
    public function update(Request $request, Quiz $quiz)
    {
        //$this->authorize('update', $quiz);
        
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
        //$this->authorize('delete', $quiz);
        $quiz->delete();

        return redirect()->route('quizzes.index')
            ->with('success', 'Quiz supprimé avec succès.');
    }

    /**
     * Afficher les questions d'un quiz
     */
    public function questions(Quiz $quiz)
    {
        //$this->authorize('update', $quiz);
        $questions = $quiz->questions;
        return view('quizzes.questions', compact('quiz', 'questions'));
    }

    /**
     * Afficher les participations à un quiz
     */
    public function participations(Quiz $quiz)
    {
        //$this->authorize('view', $quiz);
        $participations = $quiz->participations()->with('user')->latest()->get();
        return view('quizzes.participations', compact('quiz', 'participations'));
    }
}