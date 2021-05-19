<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game_Genero extends Model
{
	use HasFactory;

	protected $fillable = [
			'genero_id',
			'game_id'
		];
}
