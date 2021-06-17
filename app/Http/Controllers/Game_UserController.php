<?php

namespace App\Http\Controllers;

use App\Models\Game_User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Game_UserController extends Controller
{
	public function create_posesion(Request $request){
		$request->validate([
			'plataforma_id' =>['required']
		],[
			'plataforma_id.required' => 'Debes indicar la plataforma en la que tienes el juego'
		]);

		DB::table('game_user')->insert(['game_id' => $request->game_id,'plataforma_id' => $request->plataforma_id, 'user_id' => $request->user_id]);
	}

	public function delete_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->where('game_id',$game_id['game_id'])->where('user_id', $game_id['user_id'])->delete();
	}

	public function getJuegosSinReview(Request $request) {
		//Devuelve la lista de juegos de los que no hizo review el usuario
		return DB::SELECT("SELECT game_user.game_id, game_user.user_id, games.titulo, plataformas.nombre FROM game_user,plataformas, games where user_id = $request->user_id and game_user.game_id = games.id and game_user.plataforma_id = plataformas.id and game_user.game_id NOT IN (SELECT reviews.game_id from reviews where reviews.user_id = $request->user_id )  ");
	}

	public function getListaJuegos(Request $request){

		//Devuelve la lista de juegos de los que SI hizo review el usuario

		return DB::SELECT("SELECT reviews.game_id, reviews.juegoBase, reviews.juegoExtendido,reviews.completadoTotal, reviews.puntuacion, games.titulo FROM reviews, games where user_id = $request->user_id and reviews.game_id = games.id and reviews.game_id IN (SELECT game_user.game_id from game_user) ORDER BY `reviews`.`game_id` ASC
		");

	}

	//Devuelve las plataformas de los juegos que ha adquirido el usuario
	public function getPlataformaGameUser(Request $request){
		return DB::SELECT('SELECT plataformas.nombre FROM `game_user`, `plataformas` where user_id = ' . $request->user_id . ' AND game_id = ' . $request->game_id . ' and plataforma_id = plataformas.id ');
	}

}
