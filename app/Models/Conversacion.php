<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_1_id',
      'user_2_id',
  ];

  public function mensaje(){

    return $this->hasMany(Mensaje::class)->orderBy('created_at', 'desc');

  }


}
