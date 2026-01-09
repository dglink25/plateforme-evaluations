<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'started_at',
        'completed_at',
        'status',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Événement de démarrage (boot)
    protected static function boot()
    {
        parent::boot();

        // Observer pour vérifier automatiquement le temps écoulé
        static::saving(function ($participation) {
            $participation->checkAndAutoComplete();
        });

        // Observer pour vérifier périodiquement
        static::retrieved(function ($participation) {
            if ($participation->shouldAutoComplete()) {
                $participation->autoComplete();
            }
        });
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Accesseur pour vérifier si le temps est écoulé
    public function getTimeElapsedAttribute()
    {
        if (!$this->started_at || $this->status === 'completed') {
            return false;
        }

        $duration = $this->quiz->duration ?? 0; // durée en minutes
        $elapsedMinutes = $this->started_at->diffInMinutes(now());
        
        return $elapsedMinutes >= $duration;
    }

    // Accesseur pour le temps restant en secondes
    public function getRemainingSecondsAttribute()
    {
        if (!$this->started_at || $this->status === 'completed') {
            return 0;
        }

        $duration = $this->quiz->duration ?? 0; // durée en minutes
        $totalSeconds = $duration * 60;
        $elapsedSeconds = $this->started_at->diffInSeconds(now());
        $remaining = $totalSeconds - $elapsedSeconds;
        
        return max(0, $remaining);
    }

    // Vérifier si la participation doit être automatiquement terminée
    public function shouldAutoComplete()
    {
        return $this->status === 'in_progress' && $this->time_elapsed;
    }

    // Méthode pour vérifier et terminer automatiquement
    public function checkAndAutoComplete()
    {
        if ($this->shouldAutoComplete()) {
            $this->autoComplete();
        }
    }

    // Méthode pour terminer automatiquement la participation
    public function autoComplete()
    {
        if ($this->status !== 'completed' && $this->time_elapsed) {
            $this->status = 'completed';
            $this->completed_at = now();
            
            // Ne pas sauvegarder ici pour éviter la récursion
            // La sauvegarde se fera via save()
        }
    }

    // Méthode pour calculer et sauvegarder le score après complétion automatique
    public function calculateAndSaveAutoScore()
    {
        if ($this->status !== 'completed') {
            return;
        }

        $answers = $this->answers()->with('question')->get();
        $totalPoints = 0;
        $maxPossiblePoints = 0;

        foreach ($answers as $answer) {
            $question = $answer->question;
            $maxPossiblePoints += $question->points ?? 0;
            
            // Calculer le score pour cette réponse si pas déjà fait
            if ($answer->score === null) {
                $score = $this->calculateAnswerScore($question, $answer);
                $answer->score = $score;
                $answer->feedback = 'Corrigé automatiquement (temps écoulé)';
                $answer->save();
            }
            
            $totalPoints += $answer->score;
        }

        // Calculer le score final en pourcentage
        if ($maxPossiblePoints > 0) {
            $finalScore = ($totalPoints / $maxPossiblePoints) * 100;
            $this->score = round($finalScore, 2);
        }

        $this->save();
    }

    // Calculer le score d'une réponse spécifique
    protected function calculateAnswerScore($question, $answer)
    {
        $type = $question->type ?? null;
        
        switch ($type) {
            case 'multiple_choice':
            case 'true_false':
                $userAnswer = $answer->answer_content;
                $correctAnswer = $question->correct_answer;
                
                if ($type === 'true_false') {
                    if (is_string($userAnswer)) {
                        $userAnswer = filter_var($userAnswer, FILTER_VALIDATE_BOOLEAN);
                    }
                    if (is_string($correctAnswer)) {
                        $correctAnswer = filter_var($correctAnswer, FILTER_VALIDATE_BOOLEAN);
                    }
                }
                
                if (is_array($userAnswer) && is_array($correctAnswer)) {
                    sort($userAnswer);
                    sort($correctAnswer);
                    return $userAnswer == $correctAnswer ? $question->points : 0;
                }
                
                return $userAnswer == $correctAnswer ? $question->points : 0;
                
            default:
                // Pour les autres types, retourner 0 (nécessite correction manuelle)
                return 0;
        }
    }

    // Scope pour les participations en cours qui doivent être automatiquement terminées
    public function scopeShouldBeAutoCompleted($query)
    {
        return $query->where('status', 'in_progress')
                     ->whereHas('quiz', function ($q) {
                         $q->whereRaw("(EXTRACT(EPOCH FROM (NOW() - participations.started_at)) / 60) >= quizzes.duration");
                     });
    }

    // Scope pour les participations actives (non terminées)
    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Scope pour les participations complétées
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}