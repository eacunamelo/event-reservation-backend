<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Log in user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *         ),
 *     ),
 *     @OA\Response(response=200, description="Successful login"),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */
Route::post('/login', [AuthController::class, 'login']);

/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","password_confirmation"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *             @OA\Property(property="password_confirmation", type="string", example="password"),
 *         ),
 *     ),
 *     @OA\Response(response=201, description="Successful registration"),
 *     @OA\Response(response=400, description="Validation error")
 * )
 */
Route::post('/register', [AuthController::class, 'register']);

/**
 * @OA\Get(
 *     path="/api/spaces",
 *     summary="List all available spaces",
 *     @OA\Response(response=200, description="List of spaces")
 * )
 */
Route::get('spaces', [SpaceController::class, 'index']);

/**
 * @OA\Get(
 *     path="/api/spaces/{id}",
 *     summary="Get space by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *     ),
 *     @OA\Response(response=200, description="Space details"),
 *     @OA\Response(response=404, description="Space not found")
 * )
 */
Route::get('spaces/{id}', [SpaceController::class, 'show']);

Route::group(['middleware' => 'auth:api'], function () {

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Get current user info",
     *     @OA\Response(response=200, description="User info")
     * )
     */
    Route::get('/me', [AuthController::class, 'me']);
    
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Get reservations of current user",
     *     @OA\Response(response=200, description="List of reservations")
     * )
     */
    Route::get('reservations', [ReservationController::class, 'index']);

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Create a new reservation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"space_id", "event_name", "reservation_date", "start_time", "end_time"},
     *             @OA\Property(property="space_id", type="integer", example=1),
     *             @OA\Property(property="event_name", type="string", example="Meeting"),
     *             @OA\Property(property="reservation_date", type="string", example="2024-10-02"),
     *             @OA\Property(property="start_time", type="string", example="08:00:00"),
     *             @OA\Property(property="end_time", type="string", example="10:00:00"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Reservation created"),
     *     @OA\Response(response=409, description="Time conflict")
     * )
     */
    Route::post('reservations', [ReservationController::class, 'store']);

     /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     summary="Get reservation by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(response=200, description="Reservation details"),
     *     @OA\Response(response=404, description="Reservation not found")
     * )
     */
    Route::get('reservations/{id}', [ReservationController::class, 'show']);

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Update reservation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"space_id", "event_name", "reservation_date", "start_time", "end_time"},
     *             @OA\Property(property="space_id", type="integer", example=1),
     *             @OA\Property(property="event_name", type="string", example="Updated Meeting"),
     *             @OA\Property(property="reservation_date", type="string", example="2024-10-02"),
     *             @OA\Property(property="start_time", type="string", example="08:00:00"),
     *             @OA\Property(property="end_time", type="string", example="10:00:00"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Reservation updated"),
     *     @OA\Response(response=404, description="Reservation not found")
     * )
     */
    Route::put('reservations/{id}', [ReservationController::class, 'update']);

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Delete reservation by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(response=200, description="Reservation deleted"),
     *     @OA\Response(response=404, description="Reservation not found")
     * )
     */
    Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);

    Route::group(['middleware' => 'admin'], function() {

        /**
         * @OA\Post(
         *     path="/api/spaces",
         *     summary="Create a new space",
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"name", "capacity", "type"},
         *             @OA\Property(property="name", type="string", example="Sala A"),
         *             @OA\Property(property="capacity", type="integer", example=20),
         *             @OA\Property(property="type", type="string", example="conference_room"),
         *             @OA\Property(property="image", type="string", example="http://example.com/image.jpg"),
         *         ),
         *     ),
         *     @OA\Response(response=201, description="Space created"),
         *     @OA\Response(response=400, description="Validation error")
         * )
         */
        Route::post('spaces', [SpaceController::class, 'store']);  
        
        /**
         * @OA\Put(
         *     path="/api/spaces/{id}",
         *     summary="Update space",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         @OA\Schema(type="integer"),
         *     ),
         *     @OA\RequestBody(
         *         required=true,
         *         @OA\JsonContent(
         *             required={"name", "capacity", "type"},
         *             @OA\Property(property="name", type="string", example="Sala B"),
         *             @OA\Property(property="capacity", type="integer", example=30),
         *             @OA\Property(property="type", type="string", example="meeting_room"),
         *         ),
         *     ),
         *     @OA\Response(response=200, description="Space updated"),
         *     @OA\Response(response=404, description="Space not found")
         * )
         */
        Route::put('spaces/{id}', [SpaceController::class, 'update']);

         /**
         * @OA\Delete(
         *     path="/api/spaces/{id}",
         *     summary="Delete space by ID",
         *     @OA\Parameter(
         *         name="id",
         *         in="path",
         *         required=true,
         *         @OA\Schema(type="integer"),
         *     ),
         *     @OA\Response(response=200, description="Space deleted"),
         *     @OA\Response(response=404, description="Space not found")
         * )
         */
        Route::delete('spaces/{id}', [SpaceController::class, 'destroy']);
    });
});
