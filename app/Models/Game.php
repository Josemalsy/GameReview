<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Game extends Model
{
		use HasFactory;

		protected $fillable = [
			'titulo',
			'desarrolladora',
			'imagen',
			'lanzamiento',
		];

	public function reviews(){
		return $this->hasMany(Review::class, 'game_id')->orderBy('created_at','asc');
	}

	public function games(){
		return $this->belongsTo(Review::class);
	}

	public function Users(){
		return $this->belongsToMany(User::class);
	}

	public function Generos(){
		return $this->belongsToMany(Genero::class);
	}

	public function Plataformas(){
		return $this->belongsToMany(Plataforma::class)->orderBy('fabricante','desc');
	}

}
