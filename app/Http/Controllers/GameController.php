<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Game_Plataforma;
use App\Models\Game_Genero;


class GameController extends Controller
{

  public function index(Request $request) {
		$buscador = $request->get('filters');
    $orden = $request->get('orden');

		$columna = 'titulo';
		$tipo_orden = 'ASC';

    switch ($orden) {
      case 2:
        $columna = 'titulo';
        $tipo_orden = 'DESC';
      break;
      case 3:
        $columna = 'lanzamiento';
        $tipo_orden = 'ASC';
        break;
      case 4:
        $columna = 'lanzamiento';
        $tipo_orden = 'DESC';
        break;
      case 5:
          $columna = 'valoracion_media';
          $tipo_orden = 'DESC';
        break;
      case 6:
        $columna = 'valoracion_media';
        $tipo_orden = 'ASC';
        break;
    }

		if($buscador != null && $buscador != '') {

      return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->Where("titulo", 'like', '%' . $buscador . '%')->orWhere("desarrolladora",'like',  '%' . $buscador . '%')->orderBy($columna,$tipo_orden)->paginate(10);
    }

    return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->with('reviews')->orderBy($columna,$tipo_orden)->paginate(10);

	}

  public function addGames(Request $request) {

    $formulario_juego = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
    $imagenEnlace = request()->file('imagen')->store('caratulas', 'public');
    $array_plataformas = json_decode($request->plataformas, true);
    $array_generos = json_decode($request->generos, true);

    $juego = new Game([
      'titulo' => $formulario_juego['titulo'],
      'desarrolladora' => $formulario_juego['desarrolladora'],
      'lanzamiento' => $formulario_juego['lanzamiento'],
      'imagen' => $imagenEnlace,
    ]);
    $juego->save();

    for ($i=0; $i < count($array_plataformas) ; $i++) {
      DB::table('game_plataforma')->insert([
        'game_id' =>  $juego->id,
        'plataforma_id' => $array_plataformas[$i]['id']
      ]);
    }


    for ($i=0; $i < count($array_generos) ; $i++) {
      DB::table('game_genero')->insert([
        'game_id' =>  $juego->id,
        'genero_id' => $array_generos[$i]['id']
      ]);
    }


  }

  public function updateGame(Request $request) {

    $formulario_juego = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $array_plataformas = json_decode($request->plataformas, true);
    $array_generos = json_decode($request->generos, true);
    // $imagenEnlace = request()->file('imagen')->store('caratulas', 'public');
    // dd($imagenEnlace);
    $game = Game::find($formulario_juego['id']);
    $game->titulo = $formulario_juego['titulo'];
    $game->desarrolladora = $formulario_juego['desarrolladora'];
    $game->lanzamiento = $formulario_juego['lanzamiento'];
    if(!empty(request()->file('imagen'))){
      $imagenEnlace = request()->file('imagen')->store('caratulas', 'public');
      $game->imagen = $imagenEnlace;
    }

    $game->save();

    DB::table('game_genero')->where(['game_id'=>$formulario_juego['id']])->delete();
    DB::table('game_plataforma')->where(['game_id'=>$formulario_juego['id']])->delete();

    for ($i=0; $i < count($array_plataformas) ; $i++) {
      DB::table('game_plataforma')->insert([
        'game_id' =>  33,
        'plataforma_id' => $array_plataformas[$i]['id']
      ]);
    }

    for ($i=0; $i < count($array_generos) ; $i++) {
      DB::table('game_genero')->insert([
        'game_id' =>  33,
        'genero_id' => $array_generos[$i]['id']
      ]);
    }

  }

  public function getJuegoById($id) {
    // return Game::where('id', $id)->first();
    return Game::withCount('users')->with('reviews')->withCount('reviews')
                ->withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with('plataformas')->with('generos')
                ->where('id', $id)
                ->get();
  }

  public function destroy($id)
  {
      //
  }
}
