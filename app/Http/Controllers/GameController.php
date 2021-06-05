<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Game_Plataforma;
use App\Models\Game_Genero;
use App\Models\Game_User;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;

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

      return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->Where("titulo", 'like', '%' . $buscador . '%')->orderBy($columna,$tipo_orden)->paginate(1);
    }

    return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->with('reviews')->orderBy($columna,$tipo_orden)->paginate(1);

	}

  public function addGames(Request $request) {

    abort_if($request->plataformas['1'] != '{', 420, 'Debe seleccionar al menos una plataforma');
    abort_if($request->generos['1'] != '{', 420, 'Debe seleccionar al menos un género');

    $request->validate([
      'titulo' => ['required','max:255','unique:games'],
      'desarrolladora' => ['required','max:255'],
      'lanzamiento' => ['required'],
      'imagen' => ['required','mimes:jpg,jpeg,png,webj','max:500'],
      ],[
        'titulo.required' => 'El titulo no puede estar vacío',
        'titulo.max' => 'El titulo no puede tener más de 255 caracteres',
        'titulo.unique' => 'Ya hay un juego con ese nombre',
        'lanzamiento.required' => 'La fecha de lanzamiento no puede estar vacío',
        'imagen.required' => 'La imagen del juego es obligatoria',
        'imagen.mimes' => 'La imagen solo puede tener los siguientes formatos: PNG/JPG/JPEG/WEBJ',
        'imagen.max' => 'La imagen tiene que pesar menos de 500kb',
        'plataformas.required' => 'Debe seleccionar al menos una plataforma',
      ]);


    $imagenEnlace = request()->file('imagen')->store('caratulas', 'public');
    $array_plataformas = json_decode($request->plataformas, true);
    $array_generos = json_decode($request->generos, true);


    $juego = new Game([
      'titulo' => $request->titulo,
      'desarrolladora' => $request->desarrolladora,
      'lanzamiento' => $request->lanzamiento,
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

    abort_if($request->plataformas['1'] != '{', 420, 'Debe seleccionar al menos una plataforma');
    abort_if($request->generos['1'] != '{', 420, 'Debe seleccionar al menos un género');

    if(gettype($request->imagen) == "string"){

      $request->validate([
        'titulo' => ['required','max:255','unique:games,titulo,'. $request->id],
        'desarrolladora' => ['required','max:255'],
        'lanzamiento' => ['required'],
        ],[
          'titulo.required' => 'El titulo no puede estar vacío',
          'titulo.max' => 'El titulo no puede tener más de 255 caracteres',
          'titulo.unique' => 'Ya hay un juego con ese nombre',
          'lanzamiento.required' => 'La fecha de lanzamiento no puede estar vacío',
          'plataformas.required' => 'Debe seleccionar al menos una plataforma',
        ]);

    }else {

      $request->validate([
        'titulo' => ['required','max:255','unique:games,titulo,'. $request->id],
        'desarrolladora' => ['required','max:255'],
        'lanzamiento' => ['required'],
        'imagen' => ['required','mimes:jpg,jpeg,png,webj','max:500'],
        ],[
          'titulo.required' => 'El titulo no puede estar vacío',
          'titulo.max' => 'El titulo no puede tener más de 255 caracteres',
          'titulo.unique' => 'Ya hay un juego con ese nombre',
          'lanzamiento.required' => 'La fecha de lanzamiento no puede estar vacío',
          'plataformas.required' => 'Debe seleccionar al menos una plataforma',
          'imagen.required' => 'La imagen del juego es obligatoria',
          'imagen.mimes' => 'La imagen solo puede tener los siguientes formatos: PNG/JPG/JPEG/WEBJ',
          'imagen.max' => 'La imagen tiene que pesar menos de 500kb',
        ]);
      }

    $array_plataformas = json_decode($request->plataformas, true);
    $array_generos = json_decode($request->generos, true);

    $game = Game::find($request->id);
    $game->titulo = $request->titulo;
    $game->desarrolladora = $request->desarrolladora;
    $game->lanzamiento = $request->lanzamiento;
    if(!empty(request()->file('imagen'))){
      $imagenEnlace = request()->file('imagen')->store('caratulas', 'public');
      $game->imagen = $imagenEnlace;
    }

    $game->save();

    DB::table('game_genero')->where(['game_id'=>$request->id])->delete();
    DB::table('game_plataforma')->where(['game_id'=>$request->id])->delete();

    for ($i=0; $i < count($array_plataformas) ; $i++) {
      DB::table('game_plataforma')->insert([
        'game_id' =>  $game->id,
        'plataforma_id' => $array_plataformas[$i]['id']
      ]);
    }

    for ($i=0; $i < count($array_generos) ; $i++) {
      DB::table('game_genero')->insert([
        'game_id' =>  $game->id,
        'genero_id' => $array_generos[$i]['id']
      ]);
    }

  }

  public function getJuegoById($id,Request $request) {



    if(count(Game::where('id',$id)->get()) == 0){
      trigger_error('error');
    }else {

      $user_review = count(Review::where('game_id', $id)->where('user_id',$request->user_id)->get());

      if($user_review > 0) {

        return Game::withCount('users')->with('reviews')->withCount('reviews')
              ->withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with('plataformas')->with('generos')
              ->where('id', $id)
              ->whereHas('reviews',function (Builder $query) use($request) {
                $query->where('user_id',$request->user_id);
              })->get();

        }else {
          return Game::withCount('users')->with('reviews')->withCount('reviews')
          ->withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with('plataformas')->with('generos')
          ->where('id', $id)->get();
        }

    }

  }

  public function deleteGame(Request $request)
  {

    $game_id = $request->game_id;

    DB::table('Game_Plataforma')->where('game_id',$game_id)->delete();
    DB::table('Game_Genero')->where('game_id',$game_id)->delete();
    DB::table('Game_User')->where('game_id',$game_id)->delete();
    Review::where('game_id',$game_id)->delete();
    Game::find($game_id)->delete();
  }
}
