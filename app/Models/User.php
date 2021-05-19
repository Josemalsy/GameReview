<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Review;
use App\Models\Game;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'name',
    'email',
    'password',
    'FecNac',
    'sexo',
    'avatar',

  ];

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
  * The attributes that should be cast to native types.
  *
  * @var array
  */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function hasGames(){
		return $this->hasMany(Game::class);
  }

  public function games(){
		return $this->belongsToMany(Game::class)->orderBy('id','desc');
  }

	public function reviews(){
		return $this->hasMany(Review::class);
  }

  public function mensajes_enviados(){
		return $this->hasMany(Mensaje::class,'emisor_id','user_id');
  }

  public function mensajes_recibidos(){
		return $this->hasMany(Mensaje::class,'receptor_id','user_id');
  }

}
