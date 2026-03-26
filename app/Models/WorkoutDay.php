<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutDay extends Model
{
    protected $fillable = [
        'workout_program_id',
        'day_number',
        'day_name',
        'is_rest_day',
    ];

    public function program()
    {
        return $this->belongsTo(WorkoutProgram::class, 'workout_program_id');
    }

    public function exercises()
    {
        return $this->hasMany(WorkoutExercise::class);
    }
}
