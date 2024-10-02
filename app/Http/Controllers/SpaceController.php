<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Space;
use Illuminate\Support\Facades\Log;

class SpaceController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $capacity = $request->input('capacity');
        $date = $request->input('date');

        $query = Space::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($capacity) {
            $query->where('capacity', '>=', $capacity);
        }

        if ($date) {
            $query->whereDoesntHave('reservations', function ($q) use ($date) {
                $q->where('reservation_date', $date);
            });
        }

        $spaces = $query->get();
        return response()->json($spaces);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'type' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'spaces');
            $imageUrl = Storage::disk('spaces')->url($path);
        }

        $space = Space::create([
            'name' => $request->name,
            'description' => $request->description,
            'capacity' => $request->capacity,
            'type' => $request->type,
            'image_url' => $imageUrl,
        ]);

        return response()->json($space, 201);
    }

    public function show($id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json(['message' => 'Space not found'], 404);
        }

        return response()->json($space);
    }

    public function update(Request $request, $id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json(['message' => 'Space not found'], 404);
        }

        Log::info('name '.$request->name);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'type' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($space->image_url) {
                $path = str_replace(Storage::disk('spaces')->url(''), '', $space->image_url);
                Storage::disk('spaces')->delete($path);
            }
            $newPath = $request->file('image')->store('images', 'spaces');
            $imageUrl = Storage::disk('spaces')->url($newPath);
            $space->image_url = $imageUrl;
        }

        $space->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'capacity' => $request->input('capacity'),
            'type' => $request->input('type'),
        ]);

        return response()->json($space);
    }

    public function destroy($id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json(['message' => 'Space not found'], 404);
        }

        $space->delete();

        return response()->json(['message' => 'Space deleted successfully']);
    }
}
