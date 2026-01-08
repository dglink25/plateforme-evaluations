<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'duration',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    /**
     * Scope pour les quizzes publiés
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Vérifie si un utilisateur a déjà participé à ce quiz
     */
    public function hasUserParticipated($userId)
    {
        return $this->participations()->where('user_id', $userId)->exists();
    }

    /**
     * Récupère la participation en cours d'un utilisateur
     */
    public function getUserParticipation($userId)
    {
        return $this->participations()
            ->where('user_id', $userId)
            ->whereIn('status', ['in_progress', 'completed'])
            ->first();
    }
}