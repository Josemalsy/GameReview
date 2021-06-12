<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
  public function toArray($request){

		dd($request);
		return [
			"imagen" => !empty($this->path) ? Storage::disk('s3')->url($this->path) : '',
		];
  }
}
