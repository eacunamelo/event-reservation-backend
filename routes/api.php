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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('spaces', [SpaceController::class, 'index']);
Route::get('spaces/{id}', [SpaceController::class, 'show']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations/{id}', [ReservationController::class, 'show']);
    Route::put('reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);

    Route::group(['middleware' => 'admin'], function() {
        Route::post('spaces', [SpaceController::class, 'store']);    
        Route::put('spaces/{id}', [SpaceController::class, 'update']);
        Route::delete('spaces/{id}', [SpaceController::class, 'destroy']);
    });
});
