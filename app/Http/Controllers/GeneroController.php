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

    $request->validate([
      'nombre' => ['required','max:255','unique:generos'],
      ],[
        'nombre.required' => 'El género no puede estar vacío',
        'nombre.max' => 'El género no puede tener más de 255 caracteres',
        'nombre.unique' => 'Ya hay un género con ese nombre'
      ]);

    $genero = New Genero([
      'nombre'=> $request->nombre
    ]);
    $genero->save();

  }

  public function updateGenero(Request $request) {

    $request->validate([
      'nombre' => ['required','max:255','unique:generos,nombre,'.$request->genero_id],
      ],[
        'nombre.required' => 'El género no puede estar vacío',
        'nombre.max' => 'El género no puede tener más de 255 caracteres',
        'nombre.unique' => 'Ya hay un género con ese nombre',
      ]);

    $genero = Genero::find($request->genero_id);

    $genero->nombre = $request->nombre;
    $genero->save();

  }

  public function deleteGenero(Request $request) {
    $genero = Genero::find($request->genero_id);
    $genero->delete();
  }

  //Devuelve cuantos juegos tiene cada género
  public function getCountJuegosPorGenero(){

    return DB::SELECT('SELECT generos.nombre, count(game_genero.id) as cantidad FROM `generos`, game_genero WHERE game_genero.genero_id = generos.id GROUP BY generos.nombre');
  }


}