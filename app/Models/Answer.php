<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'participation_id',
        'question_id',
        'answer_content',
        'file_path',
        'score',
        'feedback',
    ];

    protected $casts = [
        'answer_content' => 'array',
    ];

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}