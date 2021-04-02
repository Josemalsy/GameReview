<?php

namespace App\Http\Controllers;

use App\Models\Game_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Game_UserController extends Controller
{
	public function create_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->insert(['game_id' => $game_id['params']['game_id'],'user_id' => $request->user()->id]);
	}

	public function delete_posesion(Request $request){
		$game_id = ($request->all() == null ? json_decode($request->getContent(), true) : $request->all());
		DB::table('game_user')->where('game_id',$game_id['game_id'])->where('user_id',$request->user()->id)->delete();
	}
}
