<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:api'], function () {
    // Rutas para espacios
    Route::get('spaces', [SpaceController::class, 'index']);
    Route::post('spaces', [SpaceController::class, 'store']);
    Route::get('spaces/{id}', [SpaceController::class, 'show']);
    Route::put('spaces/{id}', [SpaceController::class, 'update']);
    Route::delete('spaces/{id}', [SpaceController::class, 'destroy']);

    // Rutas para reservas
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations/{id}', [ReservationController::class, 'show']);
    Route::put('reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);
});
