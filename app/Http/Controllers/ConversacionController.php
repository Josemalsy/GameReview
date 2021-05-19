<?php

namespace App\Http\Controllers;

use App\Models\Conversacion;
use App\Models\Mensaje;
use Illuminate\Http\Request;

class ConversacionController extends Controller {

  public function getConversacionesById(Request $request){

    return Conversacion::where('id',$request->conversacion_id)->with('mensaje.emisor:id,name')->get();
  }

}