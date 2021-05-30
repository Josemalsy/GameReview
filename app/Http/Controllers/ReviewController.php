<?php

namespace App\Http\Controllers;

use App\Models\Review;
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

    $formulario_review = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $game_user = Auth::user()->games()->wherePivot('user_id', $request->user()->id)->wherePivot('game_id',$formulario_review['params']['formulario']['id_game'])->first();

    $review_user = Review::where('user_id',$request->user()->id)->where('game_id', $formulario_review['params']['formulario']['id_game'])->first();

    if(is_null($review_user)) {

      if(is_null($game_user)) {
        DB::table('game_user')->insert([
            'game_id' => $formulario_review['params']['formulario']['id_game'],
            'user_id' => $request->user()->id,
            'plataforma_id' => $formulario_review['params']['formulario']['plataforma_id']
        ]);
      }
      $review = new Review([
        'game_id' => $formulario_review['params']['formulario']['id_game'],
        'user_id' => $request->user()->id,
        'juegoBase' => $formulario_review['params']['formulario']['juego_base'],
        'juegoExtendido' => $formulario_review['params']['formulario']['juego_extendido'],
        'completadoTotal' => $formulario_review['params']['formulario']['completado_total'],
        'mensaje' => $formulario_review['params']['formulario']['mensaje'],
        'puntuacion' => $formulario_review['params']['formulario']['valoracion'],
      ]);

      $review->save();

    }else {
      trigger_error("Este usuario ya hizo una reseña");
    }

  }

  public function edit_review(Request $request){
    $formulario_review = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $game_user = Auth::user()->games()->wherePivot('user_id', $request->user()->id)->wherePivot('game_id',$formulario_review['params']['formulario']['id_game'])->first();

    $review_user = Review::where('user_id',$request->user()->id)->where('game_id', $formulario_review['params']['formulario']['id_game'])->first();

      if(is_null($game_user)) {
        DB::table('game_user')->insert([
            'game_id' => $formulario_review['params']['formulario']['id_game'],
            'user_id' => $request->user()->id,
            'plataforma_id' => $formulario_review['params']['formulario']['plataforma_id']
        ]);
      }else{

      }

      $review = Review::find($formulario_review['params']['formulario']['review_id']);

      $review->game_id = $formulario_review['params']['formulario']['id_game'];
      $review->user_id = $request->user()->id;
      $review->juegoBase = $formulario_review['params']['formulario']['juego_base'];
      $review->juegoExtendido = $formulario_review['params']['formulario']['juego_extendido'];
      $review->completadoTotal = $formulario_review['params']['formulario']['completado_total'];
      $review->mensaje = $formulario_review['params']['formulario']['mensaje'];
      $review->puntuacion = $formulario_review['params']['formulario']['valoracion'];
      $review->estado = 'Pendiente';
      $review->visto = 'No';
      $review->save();

  }

  public function getUserGameReview(Request $request){

    return Review::where('game_id',$request->game_id)->where('user_id',$request->user_id)->get();
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

    $review_array = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $review_id = $review_array['params']['review_id'];

    $review = Review::find($review_id);

    $review->estado = 'Aceptado';
    $review->visto = 'No';
    $review->save();

  }

  public function reviewRejected(Request $request) {

    $review_array = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
    $review_id = $review_array['params']['review_id'];

    $review = Review::find($review_id);
    $review->estado = 'Rechazado';
    $review->observaciones = $review_array['params']['observacion'];
    $review->visto = 'No';
    $review->save();

  }

  public function getPuntuacionesById(Request $request) {
    return Review::select('puntuacion', 'juegoBase')->where('game_id',$request->game_id)->get();
  }

}


