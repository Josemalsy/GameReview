<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\ExpulsionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Game_GeneroController;
use App\Http\Controllers\Game_UserController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\PlataformaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;




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


Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});


//Rutas de conversacion
Route::get('getConversacionesById',[ConversacionController::class,'getConversacionesById']);



//Rutas de expulsion
Route::get('getExpulsionesByUserId',[ExpulsionController::class,'getExpulsionesByUserId']);
Route::get('getUnban',[ExpulsionController::class,'getUnban']);



//Rutas de game_genero
Route::get('stats_user_genero',[Game_GeneroController::class,'getUserGenero']);



//Rutas de game_user
Route::get('muestra_juegos',[Game_UserController::class,'getListaJuegos']);
Route::get('getJuegosSinReview', [Game_UserController::class,'getJuegosSinReview']);
Route::get('getPlataformaGameUser', [Game_UserController::class,'getPlataformaGameUser']);
Route::post('game_user/create_posesion', [Game_UserController::class,'create_posesion']);
Route::delete('game_user/delete_posesion', [Game_UserController::class,'delete_posesion']);



//Rutas de genero
Route::get('getGeneros',[GeneroController::class, 'getGeneros']);
Route::get('getGenerosById',[GeneroController::class, 'getGenerosById']);
Route::post('create_genero',[GeneroController::class, 'createGenero']);
Route::post('update_genero',[GeneroController::class, 'updateGenero']);
Route::delete('delete_genero',[GeneroController::class, 'deleteGenero']);
Route::get('getCountJuegosPorGenero',[GeneroController::class, 'getCountJuegosPorGenero']);

//CASCAN LOS JUEGOS QUE NO TIENEN REVIEWS REALIZADAS

//Rutas de juegos
Route::get('juegos',[GameController::class,'index']);
Route::get('juego/{id}',[GameController::class,'getJuegoById']);
Route::post('juego/add_game',[GameController::class,'addGames']);
Route::post('juego/updateGame',[GameController::class,'updateGame']);
Route::delete('juego/delete_game',[GameController::class,'deleteGame']);


//Rutas de mensaje
Route::get('mensajes_recibidos',[MensajeController::class,'getMensajesRecibidos']);
Route::get('mensajes_enviados_no_leidos',[MensajeController::class,'getMensajesRecibidosEnviadosSinLeer']);
Route::get('mensajes_enviados_leidos',[MensajeController::class,'getMensajesRecibidosEnviadosLeidos']);
Route::get('getMensajeById',[MensajeController::class,'getMensajeById']);
Route::get('borrar_mensajes',[MensajeController::class,'borrarMensajes']);
Route::get('consultar_avisos_mensajes',[MensajeController::class,'consultarAvisosMensajes']);
Route::post('enviar_mensaje',[MensajeController::class,'enviarMensaje']);
Route::post('responder_mensaje',[MensajeController::class,'responderMensaje']);



//Rutas de plataformas
Route::get('getPlataformas',[PlataformaController::class, 'getPlataformas']);
  //ESTO DEVUELVE LAS PLATAFORMAS DE CADA JUEGO
	Route::get('getPlataformasById',[PlataformaController::class, 'getPlataformasById']);
  //ESTO DEVUELVE TODAS LAS PLATAFORMAS
Route::get('getAllPlataformasById',[PlataformaController::class, 'getAllPlataformasById']);
  //ESTO DEVUELVE CUANTOS JUEGOS TIENE CADA PLATAFORMA
Route::get('getCountJuegosPorPlataforma',[PlataformaController::class, 'getCountJuegosPorPlataforma']);
  //ESTO DEVUELVE CUANTOS JUEGOS TIENE CADA PLATAFORMA
Route::get('getCountJuegosPorFabricante',[PlataformaController::class, 'getCountJuegosPorFabricante']);
Route::get('stats_user_plataformas',[PlataformaController::class,'getPlataformasUsuarios']);
Route::get('stats_Plataformas_Games',[PlataformaController::class,'getStatsPlataformasGames']);
Route::post('create_plataforma',[PlataformaController::class, 'createPlataforma']);
Route::post('update_plataforma',[PlataformaController::class, 'updatePlataforma']);
Route::delete('delete_plataforma',[PlataformaController::class, 'deletePlataforma']);



//Rutas de reviews
Route::get('review/{id}',[ReviewController::class,'reviewsByGameId']);
Route::get('review_user/{id}',[ReviewController::class,'reviewsByUserId']);
Route::get('get_reviews/all', [ReviewController::class,'getAllReview']);
Route::get('get_puntuacionesById', [ReviewController::class,'getPuntuacionesById']);
Route::get('get_reviews_By_User_Game', [ReviewController::class,'getUserGameReview']);
Route::get('consultar_avisos_reviews',[ReviewController::class,'consultarAvisosReviews']);
Route::post('review/post_review', [ReviewController::class,'post_review']);
Route::post('review/edit_review', [ReviewController::class,'edit_review']);
Route::post('reviewAccepted', [ReviewController::class,'reviewAccepted']);
Route::post('reviewRejected', [ReviewController::class,'reviewRejected']);



//Rutas de usuario
Route::get('get_all_users',[UserController::class,'getAllUsers']);
Route::get('usuario/{id}',[UserController::class,'getUserById']);
Route::get('getAllUsers', [UserController::class, 'getUsers']);
Route::get('stats_sexo_usuarios',[UserController::class, 'getStatsSexoUsers']);
Route::get('user_list',[UserController::class, 'userList']);
Route::get('getUnban',[UserController::class,'getUnban']);
Route::post('actualizarUsuario',[UserController::class,'update']);
Route::post('ban_user',[UserController::class, 'banUser']);
Route::post('cambia_rol',[UserController::class, 'cambiarRol']);






















