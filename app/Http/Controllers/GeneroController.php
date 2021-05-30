<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneroController extends Controller {

  public function getGeneros() {

    return Genero::select('nombre','id')->orderBy('nombre')->get();

  }

  public function getGenerosById(Request $request) {

    return Genero::select('nombre','id')->where('id',$request->genero_id)->get();

  }

  public function createGenero(Request $request){
    $genero = New Genero([
      'nombre'=> $request['params']['nombreGenero']
    ]);
    $genero->save();

  }

  public function updateGenero(Request $request) {

    $genero = Genero::find($request['params']['genero_id']);

    $genero->nombre = $request['params']['nombreGenero'];
    $genero->save();

  }

  public function deleteGenero(Request $request) {
    $genero = Genero::find($request->genero_id);
    $genero->delete();
  }

  public function getCountJuegosPorGenero(){

    return DB::SELECT('SELECT generos.nombre, count(game_genero.id) as cantidad FROM `generos`, game_genero WHERE game_genero.genero_id = generos.id GROUP BY generos.nombre');
  }


}