<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    // GET /hero-slides
    public function index()
    {
        $slides = HeroSlide::all();

        return response()->json($slides);
    }

    // POST /hero-slides
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'background_image' => 'required|image|mimes:jpg,jpeg,png|max:10048',
            'is_active' => 'nullable|boolean',
        ]);

        $backgroundPath = $request->file('background_image')->store('hero_slides', 'public');

        $heroSlide = HeroSlide::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'background_image' => $backgroundPath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Hero slide created successfully!',
            'data' => $heroSlide
        ], 201);
    }

    // GET /hero-slides/{id}
    public function show($id)
    {
        $hero = HeroSlide::findOrFail($id);

        return response()->json($hero);
    }

    // PUT/PATCH /hero-slides/{id}
    public function update(Request $request, $id)
    {
        $hero = HeroSlide::findOrFail($id);

        $hero->update($request->only([
            'title',
            'subtitle',
            'background_image',
            'is_active',
        ]));

        return response()->json([
            'message' => 'Hero slide updated successfully',
            'data' => $hero
        ]);
    }

    // DELETE /hero-slides/{id}
    public function destroy($id)
    {
        $hero = HeroSlide::findOrFail($id);
        $hero->delete();

        return response()->json([
            'message' => 'Hero slide deleted successfully.'
        ]);
    }
}