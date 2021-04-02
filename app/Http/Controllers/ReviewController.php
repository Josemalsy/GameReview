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

  public function reviewsByUserId($id){
    return Review::with('games')->where('user_id',$id)->paginate(2);
  }

  public function post_review(Request $request){

    $formulario_review = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $game_user = Auth::user()->games()->wherePivot('user_id', $request->user()->id)->wherePivot('game_id',$formulario_review['params']['formulario']['id_game'])->first();

    $review_user = Review::where('user_id',$request->user()->id)->where('game_id', $formulario_review['params']['formulario']['id_game'])->first();

    if(is_null($review_user)) {

      if(is_null($game_user)) {
        DB::table('game_user')->insert([
            'game_id' => $formulario_review['params']['formulario']['id_game'],
            'user_id' => $request->user()->id
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

}


