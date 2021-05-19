<?php

namespace App\Http\Controllers;

use App\Models\Plataforma;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlataformaController extends Controller {

  public function getPlataformas() {

    return Plataforma::select('nombre','id')->orderBy('nombre')->get();

  }

  public function getPlataformasById(Request $request) {

    return Game::with('plataformas:id,nombre')->where('id',$request->game_id)->get();

  }

  public function getPlataformasUsuarios(Request $request) {
    return DB::Select("SELECT count(plataformas.id) as cantidad, plataformas.nombre from game_user, plataformas where game_user.plataforma_id = plataformas.id AND game_user.user_id = $request->user_id GROUP BY plataformas.nombre");
  }

  public function getStatsPlataformasGames(Request $request) {
    return DB::SELECT("SELECT count(game_user.game_id) AS cantidad, plataformas.nombre FROM `game_user`, plataformas WHERE game_user.game_id = $request->game_id AND game_user.plataforma_id = plataformas.id group by game_user.plataforma_id, plataformas.nombre");
  }

}