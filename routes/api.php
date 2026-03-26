<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineWorkoutController;
use App\Http\Controllers\FitnessPlanAIController;
use App\Http\Controllers\WorkoutProgramController;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\FitnessMetricController;


/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);


/*
|--------------------------------------------------------------------------
| AI Debug
|--------------------------------------------------------------------------
*/

Route::get('/ai/ping', fn() => response()->json(['ok' => true]));
Route::get('/ai/debug-config', function () {
    return response()->json([
        'openai_key_set' => (bool) config('services.openai.key'),
        'model' => config('services.openai.model'),
    ]);
});


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });


    /*
    | AI Workout Plan
    */
    Route::post('/ai/workout-plan', [FitnessPlanAIController::class, 'generate']);


    /*
    | Workout Programs
    */
    Route::post('/workout-programs/{id}/start', [WorkoutProgramController::class, 'start']);
    Route::get('/workout-programs/active', [WorkoutProgramController::class, 'active']);
    Route::get('/workout-programs/active/today', [WorkoutProgramController::class, 'today']);
    Route::post('/workout-programs/{id}/next-day', [WorkoutProgramController::class, 'nextDay']);
    Route::post('/workout-programs/{id}/skip-day', [WorkoutProgramController::class, 'skipDay']);


    /*
    | Workout Logs
    */
    Route::post('/workout-programs/{program}/days/{day}/log', [WorkoutLogController::class, 'store']);
    Route::get('/workout-programs/{program}/summary', [WorkoutLogController::class, 'summary']);
    Route::get('/workout-programs/{program}/days/{day}/log', [WorkoutLogController::class, 'show']);
    Route::get('/workout-history', [WorkoutLogController::class, 'history']);


    /*
    | Fitness Metrics
    */
    Route::get('/fitness-metrics', [FitnessMetricController::class, 'index']);
    Route::get('/fitness-metrics/latest', [FitnessMetricController::class, 'latest']);
    Route::post('/fitness-metrics', [FitnessMetricController::class, 'store']);


    /*
    | Machines
    */
    Route::post('/machines/{name}/generate-qr', [MachineController::class, 'generateQr']);

    Route::get('/machines/{machine}/workouts', [MachineWorkoutController::class, 'index']);
    Route::get('/machines/{machine}/workouts/summary', [MachineWorkoutController::class, 'summary']);
    Route::post('/machines/{machine}/workouts', [MachineWorkoutController::class, 'store']);
});


/*
|--------------------------------------------------------------------------
| Public Content
|--------------------------------------------------------------------------
*/

Route::get('/hero-slides', [HeroController::class, 'index']);
Route::get('/hero-slides/{id}', [HeroController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Machines Library (Public)
|--------------------------------------------------------------------------
*/

Route::get('/machines', [MachineController::class, 'index']);
Route::get('/machines/{machine}', [MachineController::class, 'show']);
Route::post('/machines', [MachineController::class, 'store']);
Route::put('/machines/{machine}', [MachineController::class, 'update']);
