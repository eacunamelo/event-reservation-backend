<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Space;
use Illuminate\Support\Facades\Log;

class SpaceController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/spaces",
     *     summary="Obtener espacios disponibles",
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Tipo de espacio (auditorium, meeting_room, conference_room)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="capacity",
     *         in="query",
     *         description="Capacidad mínima del espacio",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Fecha para comprobar disponibilidad",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de espacios disponibles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
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


     /**
     * @OA\Post(
     *     path="/api/spaces",
     *     summary="Crear un nuevo espacio",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "capacity", "type"},
     *             @OA\Property(property="name", type="string", example="Sala de reuniones"),
     *             @OA\Property(property="description", type="string", example="Una sala perfecta para reuniones"),
     *             @OA\Property(property="capacity", type="integer", example=20),
     *             @OA\Property(property="type", type="string", example="meeting_room"),
     *             @OA\Property(property="image", type="string", format="binary", description="Archivo de imagen opcional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Espacio creado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="space", type="object")
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/api/spaces/{id}",
     *     summary="Obtener un espacio por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del espacio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Espacio obtenido con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="space", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Espacio no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $space = Space::find($id);

        if (!$space) {
            return response()->json(['message' => 'Space not found'], 404);
        }

        return response()->json($space);
    }


    /**
     * @OA\Put(
     *     path="/api/spaces/{id}",
     *     summary="Actualizar un espacio",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del espacio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "capacity", "type"},
     *             @OA\Property(property="name", type="string", example="Sala de conferencias"),
     *             @OA\Property(property="description", type="string", example="Sala para grandes conferencias"),
     *             @OA\Property(property="capacity", type="integer", example=50),
     *             @OA\Property(property="type", type="string", example="conference_room"),
     *             @OA\Property(property="image", type="string", format="binary", description="Archivo de imagen opcional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Espacio actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="space", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Espacio no encontrado"
     *     )
     * )
     */
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


    /**
     * @OA\Delete(
     *     path="/api/spaces/{id}",
     *     summary="Eliminar un espacio",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del espacio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Espacio eliminado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Space deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Espacio no encontrado"
     *     )
     * )
     */
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
