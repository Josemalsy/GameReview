<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Game extends Model
{
    use HasFactory;

	public function reviews(){
		return $this->hasMany(Review::class, 'game_id');
	}

	public function games(){
		return $this->belongsTo(Review::class);
	}

	public function Users(){
		return $this->belongsToMany(User::class);
  }

}
