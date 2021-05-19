<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game_Plataforma extends Model
{
	use HasFactory;

	protected $fillable = [
			'plataforma_id',
			'game_id'
	];

}
