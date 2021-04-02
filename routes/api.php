<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Game_UserController;




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

//SI LARAVEL DEJA DE CREAR RUTAS SOLUCION -> PHP ARTISAN ROUTE:CACHE

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

//Rutas de juegos
Route::get('juegos',[GameController::class,'index']);
Route::get('juego/{id}',[GameController::class,'getJuegoById']);

//Rutas de usuario
Route::get('usuarios',[UserController::class,'index']);
Route::get('usuario/{id}',[UserController::class,'getUserById']);

//Rutas de reviews
Route::get('review/{id}',[ReviewController::class,'reviewsByGameId']);
Route::get('review_user/{id}',[ReviewController::class,'reviewsByUserId']);
Route::post('review/post_review', [ReviewController::class,'post_review']);

//Rutas game_user
Route::post('game_user/create_posesion', [Game_UserController::class,'create_posesion']);
Route::delete('game_user/delete_posesion', [Game_UserController::class,'delete_posesion']);
