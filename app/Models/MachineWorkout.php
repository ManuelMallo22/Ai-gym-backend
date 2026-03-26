<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineWorkout extends Model
{
    use HasFactory;

    protected $fillable = ['machine_id','user_id','weight','reps','sets','completed'];

    public function machine() { return $this->belongsTo(Machine::class); }
    public function user() { return $this->belongsTo(User::class); }
}
