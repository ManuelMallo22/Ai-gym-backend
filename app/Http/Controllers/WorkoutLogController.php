<?php

namespace App\Http\Controllers;

use App\Models\WorkoutProgram;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WorkoutLogController extends Controller
{

    public function store(Request $request, WorkoutProgram $program, $day)
{
    $user = Auth::user();
    if (!$user || $user->id !== $program->user_id) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $validated = $request->validate([
        'is_completed'       => 'required|boolean',
        'exercises_summary'  => 'nullable|array',
    ]);

    // find the day by day_number within this program
    $workoutDay = $program->days()->where('day_number', $day)->firstOrFail();

    $todayDate = now()->toDateString();

    // 🔒 if a completed log already exists for this day and date, block changes
    $existing = WorkoutLog::where('user_id', $user->id)
        ->where('workout_program_id', $program->id)
        ->where('workout_day_id', $workoutDay->id)
        ->where('date', $todayDate)
        ->first();

    if ($existing && $existing->is_completed) {
        return response()->json([
            'error' => 'Workout already completed for today. It cannot be modified.'
        ], 400);
    }

    // create or update (if it exists but not completed yet)
    $log = WorkoutLog::updateOrCreate(
        [
            'user_id'            => $user->id,
            'workout_program_id' => $program->id,
            'workout_day_id'     => $workoutDay->id,
            'date'               => $todayDate,
        ],
        [
            'is_completed'      => $validated['is_completed'],
            'exercises_summary' => $validated['exercises_summary'] ?? null,
        ]
    );

    return response()->json([
        'message' => 'Workout logged',
        'log'     => $log,
    ]);
}
    // GET /workout-programs/{program}/summary
    public function summary(WorkoutProgram $program)
    {
        $user = Auth::user();
        if (!$user || $user->id !== $program->user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $program->load('logs');

        $plannedDays = $program->duration_weeks * 7;
        $completedDays = $program->logs->where('is_completed', true)->count();
        $adherence = $plannedDays > 0
            ? round(($completedDays / $plannedDays) * 100)
            : 0;

        return response()->json([
            'program_id'      => $program->id,
            'status'          => $program->status,
            'start_date'      => $program->start_date,
            'end_date'        => $program->end_date,
            'planned_days'    => $plannedDays,
            'completed_days'  => $completedDays,
            'adherence_percent' => $adherence,
        ]);
    }


// GET /workout-programs/{program}/days/{day}/log
public function show(WorkoutProgram $program, $day)
{
    $user = Auth::user();
    if (!$user || $user->id !== $program->user_id) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $workoutDay = $program->days()->where('day_number', $day)->firstOrFail();

    $log = WorkoutLog::where('user_id', $user->id)
        ->where('workout_program_id', $program->id)
        ->where('workout_day_id', $workoutDay->id)
        ->orderByDesc('date')
        ->first();

    if (!$log) {
        return response()->json(['log' => null]);
    }

    return response()->json(['log' => $log]);
}

public function history()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $logs = WorkoutLog::where('user_id', $user->id)
        ->with(['program', 'day'])
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    $mapped = $logs->map(function ($log) {
        $exercises      = $log->exercises_summary ?? [];
        $totalTime      = 0;
        $totalSetsDone  = 0;

        foreach ($exercises as $ex) {
            $totalTime     += $ex['total_time_seconds'] ?? 0;
            $totalSetsDone += $ex['sets_completed'] ?? 0;
        }

        return [
            'id'                    => $log->id,
            'date'                  => $log->date,
            'program_id'            => $log->workout_program_id,
            'day_number'            => optional($log->day)->day_number,
            'day_name'              => optional($log->day)->day_name,
            'goal'                  => optional($log->program)->goal,
            'is_completed'          => $log->is_completed,
            'total_time_seconds'    => $totalTime,
            'total_sets_completed'  => $totalSetsDone,
        ];
    });

    return response()->json(['logs' => $mapped]);
}




}
