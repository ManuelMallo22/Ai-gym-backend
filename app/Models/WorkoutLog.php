<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'user_id',
        'workout_program_id',
        'workout_day_id',
        'date',
        'is_completed',
        'exercises_summary',
    ];

    protected $casts = [
        'exercises_summary' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(WorkoutProgram::class, 'workout_program_id');
    }

    public function day()
    {
        return $this->belongsTo(WorkoutDay::class, 'workout_day_id');
    }
}


