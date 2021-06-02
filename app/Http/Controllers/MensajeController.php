<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Conversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth;


class MensajeController extends Controller {

  public function getMensajesRecibidos(Request $request) {

    return Mensaje::where('receptor_id', $request->user_id)->with('emisor')->where('borrado_receptor','No')->orderBy('created_at','desc')->get();

  }

  public function getMensajesRecibidosEnviadosSinLeer(Request $request) {

    return Mensaje::where('emisor_id', $request->user_id)->where('leido','No')->where('borrado_emisor','No')->with('receptor')->orderBy('created_at','desc')->get();

  }

  public function getMensajesRecibidosEnviadosLeidos(Request $request) {

    return Mensaje::where('emisor_id', $request->user_id)->where('leido','Si')->where('borrado_emisor','No')->with('receptor')->orderBy('created_at','desc')->get();

  }

  public function consultarAvisosMensajes(Request $request) {

    if($request->isMensajeRoute == 'true'){
      $mensaje = DB::table('mensajes')->where('receptor_id',$request->user_id)->update(['visto' => 'Si']);
    }

    return count(Mensaje::where('receptor_id', $request->user_id)->where('visto','No')->with('receptor')->get());

  }

  public function borrarMensajes(Request $request) {

    $opcion = $request->opcion;

    if(is_array(json_decode($request->mensajes))){
      $mensajes = json_decode($request->mensajes);
      for ($i=0; $i < count($mensajes); $i++) {
        $mensaje = Mensaje::find($mensajes[$i]);
        if($opcion == 1){

          $mensaje->borrado_receptor = 'Si';
          $mensaje->save();
          $conversacion_id = $mensaje->conversacion_id;
          $conversacion = count(Mensaje::where('conversacion_id',$conversacion_id)->get());
          $mensajes_borrados = count(Mensaje::where('conversacion_id',$conversacion_id)->where('borrado_emisor','Si')->where('borrado_receptor','Si')->get());
          if($conversacion == $mensajes_borrados){
            DB::table('mensajes')->where('conversacion_id',$conversacion_id)->delete();
            $borrarConversacion = Conversacion::find($conversacion_id)->delete();
          }

        }else {

          $mensaje->borrado_emisor = 'Si';
          $mensaje->save();
          $conversacion_id = $mensaje->conversacion_id;
          $conversacion = count(Mensaje::where('conversacion_id',$conversacion_id)->get());
          $mensajes_borrados = count(Mensaje::where('conversacion_id',$conversacion_id)->where('borrado_emisor','Si')->where('borrado_receptor','Si')->get());
          if($conversacion == $mensajes_borrados){
            DB::table('mensajes')->where('conversacion_id',$conversacion_id)->delete();
            $borrarConversacion = Conversacion::find($conversacion_id)->delete();
          }

        }
      }

    }else {
      $mensaje = Mensaje::find($request->mensaje_id);
      if($opcion == 1){

        $mensaje->borrado_receptor = 'Si';
        $mensaje->save();
        $conversacion_id = $mensaje->conversacion_id;
        $conversacion = count(Mensaje::where('conversacion_id',$conversacion_id)->get());
        $mensajes_borrados = count(Mensaje::where('conversacion_id',$conversacion_id)->where('borrado_emisor','Si')->where('borrado_receptor','Si')->get());
        if($conversacion == $mensajes_borrados){
          DB::table('mensajes')->where('conversacion_id',$conversacion_id)->delete();
          $borrarConversacion = Conversacion::find($conversacion_id)->delete();
        }

      }else {

        $mensaje->borrado_emisor = 'Si';
        $mensaje->save();
        $conversacion_id = $mensaje->conversacion_id;
        $conversacion = count(Mensaje::where('conversacion_id',$conversacion_id)->get());
        $mensajes_borrados = count(Mensaje::where('conversacion_id',$conversacion_id)->where('borrado_emisor','Si')->where('borrado_receptor','Si')->get());
        if($conversacion == $mensajes_borrados){
          DB::table('mensajes')->where('conversacion_id',$conversacion_id)->delete();
          $borrarConversacion = Conversacion::find($conversacion_id)->delete();
        }

      }
    }

  }

  public function getMensajeById(Request $request){
      return Mensaje::select('titulo','receptor_id','emisor_id','mensaje','created_at')->where('id', $request->mensaje_id)->with('emisor:id,name,avatar,created_at')->with('receptor:id,name')->get();

  }

  public function enviarMensaje(Request $request) {

    $request->validate([
        'titulo' => ['required','max:100'],
        'mensaje' => ['required'],
        'receptor_id' => ['required'],
        'emisor_id' => ['required'],
      ],[
        'titulo.required' => 'El campo titulo es obligatorio',
        'titulo.max' => 'El título no puede exceder de 100 caracteres',
        'mensaje.required' => 'El mensaje no puede estar vacío',
        'receptor_id.required' => 'Debe indicar un destinatario',
        'emisor_id.required' => 'Debe indicar un emisor del mensaje'
      ]
    );

    if($request->modoEnvio == 'Mandar mensaje'){

      $conversacion = new Conversacion([
        'user_1_id' => $request->emisor_id,
        'user_2_id' => $request->receptor_id,
      ]);

    }else {
      $conversacion = new Conversacion([
        'user_1_id' => $request->emisor_id,
        'user_2_id' => $request->receptor_id['id'],
      ]);
    }
    $conversacion->save();

    $mensaje = new Mensaje([
      'conversacion_id' => $conversacion->id,
      'emisor_id' => $conversacion->user_1_id,
      'receptor_id' => $conversacion->user_2_id,
      'titulo' => $request->titulo,
      'mensaje' => $request->mensaje,
    ]);
    $mensaje->save();

  }

  public function responderMensaje(Request $request) {

    $request->validate([
      'titulo' => ['required','max:100'],
      'mensaje' => ['required'],
      'receptor_id' => ['required'],
      'emisor_id' => ['required'],
    ],[
      'titulo.required' => 'El campo titulo es obligatorio',
      'titulo.max' => 'El título no puede exceder de 100 caracteres',
      'mensaje.required' => 'El mensaje no puede estar vacío',
      'receptor_id.required' => 'Debe indicar un destinatario',
      'emisor_id.required' => 'Debe indicar un emisor del mensaje'
    ]
  );

    $mensaje = new Mensaje([
      'conversacion_id' => $request->conversacion_id,
      'emisor_id' => $request->emisor_id,
      'receptor_id' =>$request->receptor_id,
      'titulo' => 'RE:' . $request->titulo,
      'mensaje' => $request->mensaje,
    ]);
    $mensaje->save();

  }

}