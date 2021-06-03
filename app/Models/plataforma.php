<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plataforma extends Model
{
    use HasFactory;

  protected $fillable = [
    'id',
    'nombre',
    'fabricante',
	];


  public function games(){
		return $this->belongsToMany(Game::class)->orderBy('id','desc');
  }

}
