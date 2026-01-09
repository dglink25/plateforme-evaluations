<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Quiz routes
    Route::resource('quizzes', QuizController::class);
    Route::get('/quizzes/{quiz}/questions', [QuizController::class, 'questions'])->name('quizzes.questions');
    Route::get('/quizzes/{quiz}/participations', [QuizController::class, 'participations'])->name('quizzes.participations');
    
    // Routes pour les questions
    Route::prefix('quizzes/{quiz}')->group(function () {
        Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    });

    // Routes pour passer les quizzes
    Route::prefix('participant')->name('participant.')->group(function () {
        Route::get('/quizzes', [ParticipantController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}', [ParticipantController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/start', [ParticipantController::class, 'start'])->name('quizzes.start');
        
        Route::prefix('quizzes/{quiz}/participations/{participation}')->group(function () {
            Route::get('/', [ParticipantController::class, 'take'])->name('quizzes.take');
            Route::post('/submit', [ParticipantController::class, 'submit'])->name('quizzes.submit');
            Route::post('/finish', [ParticipantController::class, 'finish'])->name('quizzes.finish');
            Route::get('/result', [ParticipantController::class, 'result'])->name('quizzes.result');
        });
    });

    Route::get('/quizzes/{quiz}/participations', [QuizController::class, 'participations'])
        ->name('quizzes.participations');
    
    Route::get('/quizzes/{quiz}/participations/{participation}', [QuizController::class, 'showParticipation'])
        ->name('quizzes.participation.show');
    
    Route::post('/quizzes/{quiz}/participations/{participation}/correct', [QuizController::class, 'correctAnswer'])
        ->name('quizzes.participation.correct');
    
    Route::post('/quizzes/{quiz}/participations/{participation}/validate', [QuizController::class, 'validateCorrections'])
        ->name('quizzes.participation.validate');

    Route::post('/quizzes/{quiz}/participations/{participation}/auto-correct', [QuizController::class, 'autoCorrectAll'])
        ->name('quizzes.participation.auto-correct');
});

require __DIR__.'/auth.php';