<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

  protected $fillable = [
      'conversacion_id',
      'emisor_id',
      'receptor_id',
      'titulo',
      'mensaje',
      'leido',
      'borrado_emisor',
      'borrado_receptor',
	];

  public function emisor(){

    return $this->belongsTo(User::class,'emisor_id','id');

  }

  public function receptor(){

    return $this->belongsTo(User::class,'receptor_id','id');

  }

  public function conversacion(){

    return $this->belongsTo(Conversacion::class);

  }

}
