<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expulsion;

class Expulsion extends Model
{
		use HasFactory;

		protected $fillable = [
			'user_id',
			'causa',
			'tipo_expulsion',
		];

	public function user(){
		return $this->belongsTo(User::class);
	}

}
