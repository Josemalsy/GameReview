<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Game;

class Review extends Model
{
	use HasFactory;

	protected $fillable = [
    'game_id',
    'user_id',
    'juegoBase',
    'juegoExtendido',
    'completadoTotal',
		'mensaje',
		'puntuacion',
  ];

	public function users(){

		return $this->belongsTo(User::class, 'user_id');
	}

	public function games(){
		return $this->belongsTo(Game::class,'game_id');
	}


}
