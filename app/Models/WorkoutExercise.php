<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
    protected $fillable = [
        'workout_day_id',
        'exercise_name',
        'muscle_group',
        'sets',
        'reps',
        'rest_seconds',
        'order',
    ];

    public function day()
    {
        return $this->belongsTo(WorkoutDay::class, 'workout_day_id');
    }
}
