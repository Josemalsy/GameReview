<?php

namespace App\Http\Controllers;

use App\Models\Plataforma;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlataformaController extends Controller {

  public function getPlataformas() {

    return Plataforma::select('nombre','id','fabricante')->orderBy('nombre')->get();

  }

  //ESTO DEVUELVE LAS PLATAFORMAS DE CADA JUEGO
  public function getPlataformasById(Request $request) {

    return Game::with('plataformas:id,nombre')->where('id',$request->game_id)->get();

  }

  //ESTO DEVUELVE TODAS LAS PLATAFORMAS
  public function getAllPlataformasById(Request $request) {

    return Plataforma::select('nombre','id','fabricante')->where('id',$request->plataforma_id)->get();

  }

  public function createPlataforma(Request $request){
    $plataforma = New Plataforma([
      'nombre'=> $request['params']['nombrePlataforma'],
      'fabricante' => $request['params']['fabricantePlataforma']
    ]);
    $plataforma->save();

  }

  public function updatePlataforma(Request $request) {

    $plataforma = Plataforma::find($request['params']['plataforma_id']);

    $plataforma->nombre = $request['params']['nombrePlataforma'];
    $plataforma->fabricante = $request['params']['fabricantePlataforma'];
    $plataforma->save();

  }

  public function deletePlataforma(Request $request) {
    $plataforma = Plataforma::find($request->plataforma_id);
    $plataforma->delete();
  }

  public function getPlataformasUsuarios(Request $request) {
    return DB::Select("SELECT count(plataformas.id) as cantidad, plataformas.nombre from game_user, plataformas where game_user.plataforma_id = plataformas.id AND game_user.user_id = $request->user_id GROUP BY plataformas.nombre");
  }

  public function getStatsPlataformasGames(Request $request) {
    return DB::SELECT("SELECT count(game_user.game_id) AS cantidad, plataformas.nombre FROM `game_user`, plataformas WHERE game_user.game_id = $request->game_id AND game_user.plataforma_id = plataformas.id group by game_user.plataforma_id, plataformas.nombre");
  }

  public function getCountJuegosPorPlataforma(){
    return DB::SELECT('SELECT plataformas.nombre, count(game_plataforma.id) as cantidad FROM `plataformas`, game_plataforma WHERE game_plataforma.plataforma_id = plataformas.id GROUP BY plataformas.nombre');
  }

  public function getCountJuegosPorFabricante(){
    return DB::SELECT('SELECT plataformas.fabricante, count(game_plataforma.id) as cantidad FROM `plataformas`, game_plataforma WHERE game_plataforma.plataforma_id = plataformas.id GROUP BY plataformas.fabricante ORDER BY plataformas.fabricante ');
  }

}