<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;


class GameController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
		$buscador = $request->get('filters');
    $orden = $request->get('orden');

		$columna = 'titulo';
		$tipo_orden = 'ASC';

    if($orden=='3' || $orden=='4'){
      $columna = 'lanzamiento';
    }

    if($orden=='2' || $orden=='4'){
      $tipo_orden = 'DESC';
    }

		if($buscador != null && $buscador != '') {

      return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->Where("titulo", 'like', '%' . $buscador . '%')->orWhere("desarrolladora",'like',  '%' . $buscador . '%')->orderBy($columna,$tipo_orden)->paginate(10);
    }

    return Game::withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')->with("users")->orderBy($columna,$tipo_orden)->paginate(10);

	}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return 'hola';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getJuegoById($id)
    {
        // return Game::where('id', $id)->first();
        return Game::withCount('users')->with('reviews')->withCount('reviews')
                    ->withAvg('reviews','puntuacion')->withAvg('reviews','juegoBase')->withAvg('reviews','juegoExtendido')->withAvg('reviews','completadoTotal')
                    ->where('id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
