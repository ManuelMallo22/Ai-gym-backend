<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessMetric extends Model
{
    protected $fillable = [
        'user_id',
        'program_id',
        'gender',
        'age',
        'height_cm',
        'weight_kg',
        'goal',
        'bmi',
        'bmr',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
