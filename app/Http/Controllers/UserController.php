<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

  public function index(Request $request){

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

  public function getUserById($id){
      return User::with('reviews')->with('games')->withAvg('reviews','puntuacion')->where('id', $id)->get();
  }

  public function getUsers() {
    return User::get(['id','name']);
  }

  public function update(Request $request) {

    $formulario_user = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

    $imagenEnlace = ( !empty(request()->file('avatar')) ) ? request()->file('avatar')->store('fotos', 'public') : 'fotos/indice.png';

    $user_id = $formulario_user['id'];

    $user = User::find($user_id);
    $user->name = $formulario_user['name'];
    $user->email = $formulario_user['email'];
    $user->FecNac = $formulario_user['FecNac'];
    $user->sexo = $formulario_user['sexo'];
    $user->ocupacion = $formulario_user['ocupacion'];
    $user->ubicacion = $formulario_user['ubicacion'];
    $user->aficiones = $formulario_user['aficiones'];
    $user->biografia = $formulario_user['biografia'];
    $user->avatar = $imagenEnlace;
    $user->save();
  }

  public function getStatsSexoUsers(){
    return DB::table('users')->selectRaw('sexo, count(id) as cantidad')->groupBy('sexo')->get();
  }

}
