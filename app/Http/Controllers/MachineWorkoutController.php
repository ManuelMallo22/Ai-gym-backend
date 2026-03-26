<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineWorkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineWorkoutController extends Controller
{
    // GET /api/machines/{machine}/workouts
    public function index(Machine $machine)
    {
        $userId = Auth::id();

        $rows = MachineWorkout::where('machine_id', $machine->id)
            ->where('user_id', $userId)
            ->orderBy('created_at')
            ->get(['id','weight','reps','sets','created_at']);

        return response()->json($rows);
    }

    // GET /api/machines/{machine}/workouts/summary
    public function summary(Machine $machine)
    {
        $userId = Auth::id();

        $last = MachineWorkout::where('machine_id', $machine->id)
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();

        // Simple recommendation:
        // - no history: 20kg
        // - if last reps >= 8: +2.5kg else same
        $recommended = 20;
        if ($last) {
            $recommended = $last->reps >= 8 ? $last->weight + 2.5 : $last->weight;
        }

        return response()->json([
            'last' => $last,
            'recommended_weight' => $recommended,
        ]);
    }

    // POST /api/machines/{machine}/workouts
    public function store(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'weight' => 'required|integer|min:1|max:1000',
            'reps'   => 'required|integer|min:1|max:50',
            'sets'   => 'nullable|integer|min:1|max:20',
            'completed' => 'nullable|boolean',
        ]);

        $row = MachineWorkout::create([
            'machine_id' => $machine->id,
            'user_id'    => Auth::id(),
            'weight'     => $data['weight'],
            'reps'       => $data['reps'],
            'sets'       => $data['sets'] ?? 1,
            'completed'  => $data['completed'] ?? true,
        ]);

        return response()->json($row, 201);
    }
}
