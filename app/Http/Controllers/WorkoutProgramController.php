<?php

namespace App\Http\Controllers;

use App\Models\WorkoutProgram;
use App\Models\WorkoutDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Machine;

class WorkoutProgramController extends Controller
{
    
private function normalizeExerciseName(string $name): string
{
    $name = strtolower($name);
    $name = preg_replace('/\(.*?\)/', '', $name);      // remove stuff in parentheses
    $name = preg_replace('/[^a-z0-9]+/', ' ', $name);  // replace symbols with spaces
    $name = trim($name);
    return $name;
}

    public function start($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Only find a program that belongs to THIS user
        $program = WorkoutProgram::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$program) {
            return response()->json([
                'error' => "Access denied: You don't have permission to perform this action.",
            ], 403);
        }

        // Optional: if already active, just return it
        if ($program->status === 'active') {
            return response()->json([
                'message' => 'Program already active',
                'program' => $program,
            ]);
        }

        $program->status            = 'active';
        $program->start_date        = Carbon::today()->toDateString();
        $program->current_day_number = 1;
        $program->end_date          = null;
        $program->save();

        return response()->json([
            'message' => 'Program started',
            'program' => $program,
        ]);
    }
    public function active()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $program = WorkoutProgram::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('days.exercises')
            ->latest('start_date')
            ->first();

        if (!$program) {
            return response()->json(['program' => null]);
        }

        return response()->json(['program' => $program]);
    }

public function today()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    // Get active program for this user with its days + exercises
    $program = WorkoutProgram::where('user_id', $user->id)
        ->where('status', 'active')
        ->with(['days.exercises'])
        ->first();

    if (!$program) {
        return response()->json(['today' => null]);
    }

    $currentDayNumber = $program->current_day_number ?? 1;

    // Find the current day inside the program
    $day = $program->days
        ->where('day_number', $currentDayNumber)
        ->first();

    if (!$day) {
        // no more days -> program is finished
        $program->status = 'completed';
        $program->end_date = now()->toDateString();
        $program->save();

        return response()->json(['today' => null]);
    }

    // -------------------------------
    // ATTACH tutorial_url to exercises
    // -------------------------------
    $exercises = $day->exercises;

    if ($exercises && $exercises->count() > 0) {
        // 1) Load all machines that have a tutorial_url
        $machines = Machine::whereNotNull('tutorial_url')->get();

        // 2) Build map: normalized(machine name) => tutorial_url
        $machineMap = [];
        foreach ($machines as $machine) {
            $key = $this->normalizeExerciseName($machine->name);
            $machineMap[$key] = $machine->tutorial_url;
        }

        // 3) For each exercise, attach tutorial_url if we find a match
        $exercises->transform(function ($exercise) use ($machineMap) {
            $name = $exercise->exercise_name ?? $exercise->name ?? '';
            $key  = $this->normalizeExerciseName($name);

            $exercise->tutorial_url = $machineMap[$key] ?? null;

            return $exercise;
        });
    }

    return response()->json([
        'today' => [
            'program_id'  => $program->id,
            'day_number'  => $day->day_number,
            'day_name'    => $day->day_name,
            'is_rest_day' => (bool) $day->is_rest_day,
            'notes'       => $day->notes ?? null,   // optional, handy for rest days
            'exercises'   => $exercises,
        ],
    ]);
}




public function nextDay($id)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $program = WorkoutProgram::where('id', $id)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->with(['days.exercises'])
        ->first();

    if (!$program) {
        return response()->json(['error' => 'Program not found'], 404);
    }

    $maxDay = $program->days->max('day_number') ?? 0;
    if ($maxDay === 0) {
        return response()->json(['error' => 'Program has no days'], 400);
    }

    // if already at last day -> complete program
    if (($program->current_day_number ?? 1) >= $maxDay) {
        $program->status = 'completed';
        $program->end_date = now()->toDateString();
        $program->save();

        return response()->json([
            'message' => 'Program finished. No next day.',
            'today'   => null,
        ]);
    }

    // move to next day
    $current = $program->current_day_number ?? 1;
    $program->current_day_number = $current + 1;
    $program->save();

    $nextDay = $program->days
        ->where('day_number', $program->current_day_number)
        ->first();

    return response()->json([
        'message' => 'Moved to next day',
        'today'   => [
            'program_id'  => $program->id,
            'day_number'  => $nextDay->day_number,
            'day_name'    => $nextDay->day_name,
            'is_rest_day' => (bool) $nextDay->is_rest_day,
            'exercises'   => $nextDay->exercises,
        ],
    ]);
}

public function skipDay($id)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $program = WorkoutProgram::where('id', $id)
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->with(['days.exercises'])
        ->first();

    if (!$program) {
        return response()->json(['error' => 'Program not found'], 404);
    }

    $maxDay = $program->days->max('day_number') ?? 0;
    if ($maxDay === 0) {
        return response()->json(['error' => 'Program has no days'], 400);
    }

    if (($program->current_day_number ?? 1) >= $maxDay) {
        $program->status = 'completed';
        $program->end_date = now()->toDateString();
        $program->save();

        return response()->json([
            'message' => 'Program finished. No day to skip to.',
            'today'   => null,
        ]);
    }

    // Just advance without creating a log (true "skip")
    $current = $program->current_day_number ?? 1;
    $program->current_day_number = $current + 1;
    $program->save();

    $nextDay = $program->days
        ->where('day_number', $program->current_day_number)
        ->first();

    return response()->json([
        'message' => 'Workout skipped. Moved to next day.',
        'today'   => [
            'program_id'  => $program->id,
            'day_number'  => $nextDay->day_number,
            'day_name'    => $nextDay->day_name,
            'is_rest_day' => (bool) $nextDay->is_rest_day,
            'exercises'   => $nextDay->exercises,
        ],
    ]);
}



}
