<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Games;
use App\Models\Game_User;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReviewController extends Controller
{
  public function reviewsByGameId($id){
    return Review::with('users')->where('game_id',$id)->paginate(2);
  }

  public function reviewsByUserId(Request $request){
    $id = $request->id;

    if($request->opcion == 1) {
      return Review::with('games')->where('user_id',$id)->orderBy('created_at','DESC')->paginate(10);

    }else{
      switch ($request->opcion) {
        case 2:
          $estado = 'Aceptado';
          break;
        case 3:
          $estado = 'Pendiente';
        break;
        case 4:
          $estado = 'Rechazado';
          break;
      }

      return Review::with('games')->where('user_id',$id)->where('estado',$estado)->orderBy('created_at','DESC')->paginate(10);

    }

  }

  public function post_review(Request $request){


    $request->validate([
        'id_game' => ['required'],
        'juego_base' => ['required', 'numeric'],
        'juego_extendido' => ['numeric','nullable','min:'.$request->juego_base, 'max:'.$request->completado_total],
        'completado_total' => ['numeric','nullable',],
        'mensaje' => ['required'],
        'valoracion' => ['required', 'numeric','min:0','max:100'],
        'plataforma_id' => ['required'],
        'user_id' => ['required'],
        ],[
        'id_game.required' => 'El id del juego es obligatorio',
        'juego_base.required' => 'La duración del juego base es obligatoria',
        'juego_base.numeric' => 'El campo juego base debe ser numérico',
        'juego_extendido.numeric' => 'El campo juego extendido debe ser numérico',
        'juego_extendido.min' => 'Las horas de juego extendido no pueden ser inferior a juego base',
        'valoracion.required' => 'La valoración del juego es obligatoria',
        'valoracion.min' => 'La valoración no puede ser inferior a 0',
        'valoracion.max' => 'La valoración no puede ser superior a 100',
        'juego_extendido.max' => 'Las horas de juego extendido no pueden ser superior a completado total',
        'completado_total.numeric' => 'El campo completado total debe ser numérico',
        'mensaje.required' => 'Debe introducir un mensaje',
        'plataforma_id.required' => 'Debe seleccionar la plataforma',
        'user_id' => 'El id del usuario es obligatorio',
        ]
      );


    $game_user = DB::SELECT('select * from game_user where user_id = ' . $request->user_id . ' AND game_id = ' . $request->id_game);

    $review_user = Review::where('user_id',$request->user_id)->where('game_id', $request->id_game)->where('estado','!=','Rechazado')->first();

    if(is_null($review_user)) {
      if(is_null($game_user)) {
        DB::table('game_user')->insert([
            'game_id' => $request->id_game,
            'user_id' => $request->user_id,
            'plataforma_id' => $request->plataforma_id
        ]);
      }
      $review = new Review([
        'game_id' => $request->id_game,
        'user_id' => $request->user_id,
        'juegoBase' => $request->juego_base,
        'juegoExtendido' => $request->juego_extendido,
        'completadoTotal' => $request->completado_total,
        'mensaje' => $request->mensaje,
        'puntuacion' => $request->valoracion,
      ]);

      $review->save();

    }else {
      trigger_error("Este usuario ya hizo una reseña");
    }

  }

  public function edit_review(Request $request){

    $request->validate([
      'id_game' => ['required'],
      'juego_base' => ['required', 'numeric'],
      'juego_extendido' => ['numeric','nullable','min:'.$request->juego_base, 'max:'.$request->completado_total],
      'completado_total' => ['numeric','nullable',],
      'mensaje' => ['required'],
      'valoracion' => ['required', 'numeric','min:0','max:100'],
      'plataforma_id' => ['required'],
      'user_id' => ['required'],
      ],[
      'id_game.required' => 'El id del juego es obligatorio',
      'juego_base.required' => 'La duración del juego base es obligatoria',
      'juego_base.numeric' => 'El campo juego base debe ser numérico',
      'juego_extendido.numeric' => 'El campo juego extendido debe ser numérico',
      'juego_extendido.min' => 'Las horas de juego extendido no pueden ser inferior a juego base',
      'juego_extendido.max' => 'Las horas de juego extendido no pueden ser superior a completado total',
      'completado_total.numeric' => 'El campo completado total debe ser numérico',
      'mensaje.required' => 'Debe introducir un mensaje',
      'plataforma_id.required' => 'Debe seleccionar la plataforma',
      'user_id' => 'El id del usuario es obligatorio',
      ]
    );


    $game_user = DB::SELECT('select * from game_user where user_id = ' . $request->user_id . ' AND game_id = ' . $request->id_game);

    $review_user = Review::where('user_id',$request->id_game)->where('game_id', $request->id_game)->first();

      if(is_null($game_user)) {
        DB::table('game_user')->insert([
            'game_id' => $request->id_game,
            'user_id' => $request->id_game,
            'plataforma_id' => $request->plataforma_id
        ]);
      }else{

      }

      $review = Review::find($request->review_id);

      $review->game_id = $request->id_game;
      $review->user_id = $request->user_id;
      $review->juegoBase = $request->juego_base;
      $review->juegoExtendido = $request->juego_extendido;
      $review->completadoTotal = $request->completado_total;
      $review->mensaje = $request->mensaje;
      $review->puntuacion = $request->valoracion;
      $review->estado = 'Pendiente';
      $review->visto = 'No';
      $review->save();

  }

  public function getUserGameReview(Request $request){
    return Review::where('id',$request->review_id)->get();
  }

  public function consultarAvisosReviews(Request $request) {

    if($request->isReviewsRoute == 'true'){
      $review = DB::table('reviews')->where('user_id', $request->user_id)->update(['visto' => 'Si']);
    }

    return DB::table('reviews')->selectRaw('count(reviews.id) as cantidad, estado')->where('user_id',$request->user_id)->where('estado','!=','pendiente')->where('visto','No')->groupBy('estado')->get();

  }

  public function getAllReview(){

    return Review::with('users')->with('games1')->where('estado','pendiente')->get();
  }

  public function reviewAccepted(Request $request) {

    $request->validate([
      'review_id' => ['required']
      ],[
      'review_id.required' => 'Debe indicar el id de la review'
    ]);

    $review = Review::find($request->review_id);

    $review->estado = 'Aceptado';
    $review->visto = 'No';
    $review->save();

  }

  public function reviewRejected(Request $request) {

    $request->validate([
      'review_id' => ['required'],
      'observacion' => ['required', 'string','max:25']
      ],[
      'review_id.required' => 'Debe indicar el id de la review',
      'observacion.required' => 'Debe indicar una causa para rechazar la review',
      'observacion.string' => 'La observación debe ser una cadena',
      'observacion.max' => 'La observación no puede tener más de 255 caracteres'
    ]);

    $review = Review::find($request->review_id);
    $review->estado = 'Rechazado';
    $review->observaciones = $request->observacion;
    $review->visto = 'No';
    $review->save();

  }

  public function getPuntuacionesById(Request $request) {
    return Review::select('puntuacion', 'juegoBase')->where('game_id',$request->game_id)->get();
  }

}


