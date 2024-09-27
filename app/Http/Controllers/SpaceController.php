<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;

class SpaceController extends Controller
{
    public function index()
    {
        $spaces = Space::all();
        return response()->json($spaces);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'type' => 'required|string',
        ]);

        $space = Space::create($request->all());

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

        $space->update($request->all());

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
