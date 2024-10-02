<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Obtener todas las reservas del usuario autenticado",
     *     @OA\Response(
     *         response=200,
     *         description="Listado de reservas del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="reservations", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $reservations = Reservation::with('space')->where('user_id', auth()->id())->get();
        return response()->json($reservations);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Crear una nueva reserva",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"space_id", "event_name", "reservation_date", "start_time", "end_time"},
     *             @OA\Property(property="space_id", type="integer", example=1),
     *             @OA\Property(property="event_name", type="string", example="Conferencia"),
     *             @OA\Property(property="reservation_date", type="string", format="date", example="2024-10-10"),
     *             @OA\Property(property="start_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="12:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reserva creada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="El espacio ya está reservado en ese horario"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'event_name' => 'required|string|max:255',
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        $overlap = Reservation::where('space_id', $request->space_id)
            ->where('reservation_date', $request->reservation_date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                      });
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'Space is already reserved during this time'], 409);
        }

        $reservation = auth()->user()->reservations()->create($request->all());

        return response()->json($reservation, 201);
    }


    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     summary="Obtener una reserva por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva obtenida con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $reservation = auth()->user()->reservations()->with('space')->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        return response()->json($reservation);
    }


    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Actualizar una reserva",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"event_name", "reservation_date", "start_time", "end_time"},
     *             @OA\Property(property="event_name", type="string", example="Conferencia Actualizada"),
     *             @OA\Property(property="reservation_date", type="string", format="date", example="2024-10-12"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="11:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva actualizada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="reservation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $reservation = auth()->user()->reservations()->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservation->update($request->all());

        return response()->json($reservation);
    }


    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Eliminar una reserva",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la reserva",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reserva eliminada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reservation deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reserva no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        $reservation = auth()->user()->reservations()->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully']);
    }
}
