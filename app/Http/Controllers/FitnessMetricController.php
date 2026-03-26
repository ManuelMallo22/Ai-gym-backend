<?php

namespace App\Http\Controllers;

use App\Models\FitnessMetric;
use Illuminate\Http\Request;

class FitnessMetricController extends Controller
{
    // GET /fitness-metrics  → full history for logged-in user
    public function index(Request $request)
    {
        $user = $request->user();

        $metrics = FitnessMetric::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'metrics' => $metrics,
        ]);
    }

    // GET /fitness-metrics/latest  → last snapshot
    public function latest(Request $request)
    {
        $user = $request->user();

        $metric = FitnessMetric::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'metric' => $metric,
        ]);
    }

    // POST /fitness-metrics  → create new snapshot
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'program_id' => 'nullable|integer',
            'gender'     => 'nullable|string|max:10',
            'age'        => 'nullable|integer|min:1|max:120',
            'height_cm'  => 'nullable|numeric|min:50|max:250',
            'weight_kg'  => 'nullable|numeric|min:20|max:400',
            'goal'       => 'nullable|string|max:255',
        ]);

        $gender    = $data['gender']    ?? null;
        $age       = $data['age']       ?? null;
        $height_cm = $data['height_cm'] ?? null;
        $weight_kg = $data['weight_kg'] ?? null;

        // BMI
        $bmi = null;
        if ($height_cm && $weight_kg) {
            $hMeters = $height_cm / 100;
            if ($hMeters > 0) {
                $bmi = $weight_kg / ($hMeters * $hMeters);
            }
        }

        // BMR (Mifflin-St Jeor)
        $bmr = null;
        if ($height_cm && $weight_kg && $age && $gender) {
            if (strtolower($gender) === 'male') {
                $bmr = 10 * $weight_kg + 6.25 * $height_cm - 5 * $age + 5;
            } elseif (strtolower($gender) === 'female') {
                $bmr = 10 * $weight_kg + 6.25 * $height_cm - 5 * $age - 161;
            }
        }

        $metric = FitnessMetric::create([
            'user_id'    => $user->id,
            'program_id' => $data['program_id'] ?? null,
            'gender'     => $gender,
            'age'        => $age,
            'height_cm'  => $height_cm,
            'weight_kg'  => $weight_kg,
            'goal'       => $data['goal'] ?? null,
            'bmi'        => $bmi,
            'bmr'        => $bmr,
        ]);

        return response()->json([
            'metric' => $metric,
        ], 201);
    }
}
