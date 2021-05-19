<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game_User extends Model
{
	use HasFactory;

	protected $fillable = [
			'user_id',
			'game_id'
		];

}
