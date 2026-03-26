<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutProgram extends Model
{
    protected $fillable = [
        'user_id',
        'goal',
        'fitness_level',
        'duration_weeks',
        'start_date',
        'end_date',
        'status',
        'diet_plan',
        'ai_request',
        'ai_raw_response',
        'current_day_number',
    ];

    protected $casts = [
        'diet_plan'  => 'array',
        'ai_request' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function days()
    {
        return $this->hasMany(WorkoutDay::class);
    }
    public function logs()
{
    return $this->hasMany(WorkoutLog::class);
}

}
