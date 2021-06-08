<?php

namespace App\Http\Controllers;

use App\Models\Game_Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Game_GeneroController extends Controller
{
	public function create_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->insert(['game_id' => $game_id['params']['game_id'],'user_id' => $request->user()->id]);
	}

	public function delete_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->where('game_id',$game_id['game_id'])->where('user_id',$request->user()->id)->delete();
	}

	public function getUserGenero(Request $request){

		//Esto devuelve las estadisticas de los generos de los juegos que cada usuario ha jugado
		return DB::select("select count(game_genero.genero_id) as cantidad,generos.nombre from games, game_user, game_genero, generos where generos.id = game_genero.genero_id AND game_genero.game_id = games.id and games.id = game_user.game_id and game_user.user_id = $request->user_id group by game_genero.genero_id, generos.nombre");
	}
}