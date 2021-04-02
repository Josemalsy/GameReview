<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
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

		$columna = 'name';
    $tipo_orden = 'ASC';

    if($orden=='3' || $orden=='4'){
      $columna = 'created_at';
    }

    if($orden=='2' || $orden=='4'){
      $tipo_orden = 'DESC';
    }

		if($buscador != null && $buscador != '') {
      return User::with('games')->withCount('games')->withCount('reviews')->Where("name", 'like', '%' . $buscador . '%')->orderBy($columna,$tipo_orden)->paginate(10);
    }
    return User::with('games')->withCount('reviews')->withCount('games')->orderBy($columna,$tipo_orden)->paginate(10);
  }

  public function getUserById($id)
  {
      // return Game::where('id', $id)->first();
      return User::with('reviews')->with('games')->withAvg('reviews','puntuacion')->where('id', $id)->get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Game::where('id', $id)->first();
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
