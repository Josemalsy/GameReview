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
		$formulario = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());

		DB::table('game_user')->insert(['game_id' => $formulario['params']['game_id'],'plataforma_id' => $formulario['params']['plataforma_id'], 'user_id' => $formulario['params']['user_id']]);
	}

	public function delete_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->where('game_id',$game_id['game_id'])->where('user_id', $game_id['params']['user_id'])->delete();
	}

	public function getJuegosSinReview(Request $request) {
		$juego_id = $request->juego_id;

		return DB::SELECT("SELECT game_user.game_id, game_user.user_id, games.titulo, plataformas.nombre FROM game_user,plataformas, games where user_id = $request->user_id and game_user.game_id = games.id and game_user.plataforma_id = plataformas.id and game_user.game_id NOT IN (SELECT reviews.game_id from reviews)  ");
	}

	public function getListaJuegos(Request $request){


		return DB::SELECT("SELECT reviews.game_id, reviews.juegoBase, reviews.juegoExtendido,reviews.completadoTotal, reviews.puntuacion, games.titulo FROM reviews, games where user_id = $request->user_id and reviews.game_id = games.id and reviews.game_id IN (SELECT game_user.game_id from game_user) ORDER BY `reviews`.`game_id` ASC
		");

	}

	public function getPlataformaGameUser(Request $request){
		return DB::SELECT('SELECT plataformas.nombre FROM `game_user`, `plataformas` where user_id = ' . $request->user_id . ' AND game_id = ' . $request->game_id . ' and plataforma_id = plataformas.id ');
	}

}
