<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'tutorial_url',  
        'qr_code_path',
    ];


    public function workouts() {
    return $this->hasMany(\App\Models\MachineWorkout::class);
}

}
