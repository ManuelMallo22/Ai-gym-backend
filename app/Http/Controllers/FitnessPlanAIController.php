<?php

namespace App\Http\Controllers;

use App\Models\WorkoutProgram;
use App\Models\WorkoutDay;
use App\Models\WorkoutExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class FitnessPlanAIController extends Controller
{
    public function generate(Request $request)
    {
        // 1) Ensure user is logged in
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'error' => 'Unauthenticated'
            ], 401);
        }

        // 2) Validate input coming from Postman / React
        $data = $request->validate([
            'gender'           => 'required|string',
            'age'              => 'required|integer',
            'height_cm'        => 'required|numeric',
            'weight_kg'        => 'required|numeric',
            'goal'             => 'required|string',
            'fitness_level'    => 'required|string',
            'days_per_week'    => 'required|integer|min:1|max:7',
            'injuries'         => 'nullable|string',
            'diet_preferences' => 'nullable|string',
        ]);

        $injuries  = $data['injuries'] ?? 'none';
        $dietPrefs = $data['diet_preferences'] ?? 'no special preference';

        // 3) Build JSON-oriented prompt
        $userContent = "
You are an expert fitness and nutrition coach.

Create a 1-week training program (7 days) and a 7-day diet plan for this user.

User data:
- Gender: {$data['gender']}
- Age: {$data['age']}
- Height: {$data['height_cm']} cm
- Weight: {$data['weight_kg']} kg
- Goal: {$data['goal']}
- Fitness level: {$data['fitness_level']}
- Days per week to train: {$data['days_per_week']}
- Injuries or limitations: {$injuries}
- Diet preferences: {$dietPrefs}

CRITICAL INSTRUCTIONS:
- Respond ONLY with valid JSON.
- Do NOT include any markdown, backticks, or explanations.
- Use this EXACT JSON schema:

{
  \"overview\": {
    \"summary\": \"2-3 sentences overview of the program\",
    \"notes\": \"short practical notes for the user\"
  },
  \"week_workout\": [
    {
      \"day_number\": 1,
      \"day_name\": \"Day 1 - Upper Body\",
      \"is_rest_day\": false,
      \"exercises\": [
        {
          \"exercise_name\": \"Bench Press\",
          \"muscle_group\": \"Chest\",
          \"sets\": 4,
          \"reps\": \"8-10\",
          \"rest_seconds\": 90
        }
      ]
    }
  ],
  \"diet_plan\": [
    {
      \"day_number\": 1,
      \"day_name\": \"Day 1\",
      \"breakfast\": \"...\",
      \"lunch\": \"...\",
      \"dinner\": \"...\",
      \"snacks\": \"...\"
    }
  ]
}

Rules:
- week_workout MUST have EXACTLY 7 items (day_number 1..7).
- Each non-rest day: max 5 exercises.
- Rest days must have is_rest_day = true and exercises = [].
- diet_plan MUST have EXACTLY 7 items (day_number 1..7).
";

        // 4) HTTP options (disable SSL verify only in local)
        $options = [];
        if (app()->environment('local')) {
            $options['verify'] = false;
        }

        // 5) Call Clarifai
        $response = Http::withOptions($options)
            ->withHeaders([
                'Authorization' => 'Bearer ' . env('CLARIFAI_PAT'),
                'Content-Type'  => 'application/json',
            ])
            ->post(env('CLARIFAI_BASE_URL') . '/chat/completions', [
                'model'    => env('CLARIFAI_MODEL'),
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'You are an expert fitness and nutrition coach.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $userContent,
                    ],
                ],
                'max_completion_tokens' => 4000,
                'temperature'           => 0.7,
            ]);

        if ($response->failed()) {
            return response()->json([
                'error'   => 'Clarifai HTTP request failed',
                'details' => $response->json(),
            ], 500);
        }

        $body    = $response->json();
        $content = $body['choices'][0]['message']['content'] ?? '';

        // 6) Parse JSON
        $plan = json_decode($content, true);
        if (!$plan || !isset($plan['week_workout']) || !isset($plan['diet_plan'])) {
            return response()->json([
                'error'   => 'Failed to parse AI JSON response',
                'raw'     => $content,
            ], 500);
        }

        // 7) Save program in DB
        $program = WorkoutProgram::create([
            'user_id'        => $user->id,
            'goal'           => $data['goal'],
            'fitness_level'  => $data['fitness_level'],
            'duration_weeks' => 4, // We can reuse this 1 week for 4 weeks
            'status'         => 'draft',
            'diet_plan'      => $plan['diet_plan'],
            'ai_request'     => $data,
            'ai_raw_response'=> $content,
        ]);

        // 8) Save days + exercises
        foreach ($plan['week_workout'] as $dayItem) {
            $day = WorkoutDay::create([
                'workout_program_id' => $program->id,
                'day_number'         => $dayItem['day_number'],
                'day_name'           => $dayItem['day_name'] ?? ('Day ' . $dayItem['day_number']),
                'is_rest_day'        => $dayItem['is_rest_day'] ?? false,
            ]);

            if (!empty($dayItem['exercises']) && is_array($dayItem['exercises'])) {
                $order = 1;
                foreach ($dayItem['exercises'] as $ex) {
                    WorkoutExercise::create([
                        'workout_day_id' => $day->id,
                        'exercise_name'  => $ex['exercise_name'] ?? 'Unknown',
                        'muscle_group'   => $ex['muscle_group'] ?? null,
                        'sets'           => $ex['sets'] ?? null,
                        'reps'           => $ex['reps'] ?? null,
                        'rest_seconds'   => $ex['rest_seconds'] ?? null,
                        'order'          => $order++,
                    ]);
                }
            }
        }

        // 9) Load program with relations to send to frontend
        $program->load('days.exercises');

        return response()->json([
            'program_id' => $program->id,
            'plan'       => [
                'overview'     => $plan['overview'] ?? null,
                'week_workout' => $plan['week_workout'],
                'diet_plan'    => $plan['diet_plan'],
            ],
        ], 200);
    }
}
