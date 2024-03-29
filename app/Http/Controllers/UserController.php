<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Expulsion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

  public function __construct(){
    // $this->middleware('auth');
  }

  public function getAllUsers(Request $request){

		$buscador = $request->filters;
    $orden = $request->orden;

		$columna = 'name';
    $tipo_orden = 'ASC';

    if($orden=='3' || $orden=='4'){
      $columna = 'created_at';
    }

    if($orden=='2' || $orden=='4'){
      $tipo_orden = 'DESC';
    }

		if($buscador != null && $buscador != '') {
      return User::with('games')->withCount('games')->withCount('reviews')->Where("name", 'like', '%' . $buscador . '%')->orderBy($columna,$tipo_orden)->paginate(10);
    }
    return User::with('games')->withCount('reviews')->withCount('games')->orderBy($columna,$tipo_orden)->paginate(10);
  }

  public function getUserById($id){

    //Comprueba si existe o no el usuario solicitado. En caso negativo se lanza un error que se recogerá en cliente
    if(count(User::where('id',$id)->get()) == 0){
      trigger_error('error');
    }else {
      return User::with('reviews')->with('games')->withAvg('reviews','puntuacion')->where('id', $id)->get();
    }

  }

  public function userList(Request $request){

    //Buscador -> busca por el nombre de usuario.
    //Estado -> separa los expulsados de los no expulsados para mostrarlos en listas distintas.

    $buscador = $request->get('filters');

    $estado = ($request->get('estado') == 'true') ? 'Expulsado' : null;

    return User::select('name','id','estado','rol')->Where("name", 'like', '%' . $buscador . '%')->where('estado', $estado)->orderBy('rol')->get();
  }

  public function getUsers() {
    return User::get(['id','name']);
  }

  public function update(Request $request) {

    $user = User::find($request->id);

    abort_if(!Hash::check($request->current_password, $user->password), 420,'La contraseña actual no coincide con la de la base de datos');

    if(empty(request()->file('avatar'))){
      $request->validate([
        'name' => ['required','unique:users,name,'. $request->id, 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$request->id ],
        'password' => ['confirmed','confirmed'],
        'FecNac' => ['required'],
        'sexo' => ['required'],
        'current_password' => 'required',
        ],[
        'name.required' => 'El campo nombre es obligatorio',
        'name.unique' => 'El nombre de usuario ya está en uso',
        'name.max' => 'El nombre no puede ocupar mas de 255 caracteres',
        'email.required' => 'El campo correo electrónico es obligatorio',
        'email.unique' => 'El correo electrónico ya está en uso',
        'email.max' => 'El correo electrónico no puede ocupar mas de 255 caracteres',
        'password.required' => 'Debe introducir una contraseña',
        'password.confirmed' => 'Las contraseñas no coinciden',
        'password.min' => 'La contraseña debe tener más de 8 caracteres',
        'current_password.current_password' => 'La contraseña actual no coincide',
        'current_password.required' => 'Debes indicar tu contraseña actual',
        'FecNac.required' => 'Debe introducir una fecha de nacimiento',
        'sexo.required' => 'Debe introducir su género',
        ]
      );
    }else {
      $request->validate([
          'name' => ['required','unique:users,name,'. $request->id, 'string', 'max:255'],
          'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$request->id ],
          'password' => ['confirmed'],
          'FecNac' => ['required'],
          'sexo' => ['required'],
          'avatar' => ['mimes:jpg,jpeg,png,webj','max:20'],
          'current_password' => 'required',
        ],[
          'name.required' => 'El campo nombre es obligatorio',
          'name.unique' => 'El nombre de usuario ya está en uso',
          'name.max' => 'El nombre no puede ocupar mas de 255 caracteres',
          'email.required' => 'El campo correo electrónico es obligatorio',
          'email.unique' => 'El correo electrónico ya está en uso',
          'email.max' => 'El correo electrónico no puede ocupar mas de 255 caracteres',
          'password.required' => 'Debe introducir una contraseña',
          'password.confirmed' => 'Las contraseñas no coinciden',
          'password.min' => 'La contraseña debe tener más de 8 caracteres',
          'current_password.current_password' => 'La contraseña actual no coincide',
          'current_password.required' => 'Debes indicar tu contraseña actual',
          'FecNac.required' => 'Debe introducir una fecha de nacimiento',
          'sexo.required' => 'Debe introducir su género',
          'avatar.mimes' => 'El avatar debe ser formato  PNG/JPG/JPEG/WEBJ',
          'avatar.max' => 'El avatar no puede pesar mas de 20kb',
          'avatar.required' => 'Es obligatorio ponerse un avatar',
        ]
      );
    }

    if($request->imagenBorrada == 'nueva foto'){
      $imagenEnlace = request()->file('avatar')->store('fotos', ['disk' => 's3', 'visibility' => 'public']);
      $user->avatar = $imagenEnlace;
    }else if($request->imagenBorrada == true) {
      $imagenEnlace = 'fotos/indice.png';
      $user->avatar = $imagenEnlace;
    }


    $user->name = $request->name;
    $user->email = $request->email;
    $user->FecNac = $request->FecNac;
    $user->sexo = $request->sexo;
    if ( !empty($request->input('password')) ){
      $user->password =  Hash::make($request->password);
    }
    $user->ocupacion = $request->ocupacion;
    $user->ubicacion = $request->ubicacion;
    $user->aficiones = $request->aficiones;
    $user->biografia = $request->biografia;
    $user->save();
  }

  public function banUser(Request $request) {

    $request->validate([
      'revocar' => ['required'],
      'user_id' => ['required']
      ],[
        'revocar.required' => 'El campo revocar es obligatorio',
        'user_id.required' => 'Debe especificar un nombre de usuario'
      ]);


    $user = $user = User::find($request->user_id);

    if($request->revocar == true){


      $expulsion = Expulsion::where('user_id',$request->user_id)->orderBy('id','desc')->first();

      $expulsion->delete();

      $user->estado = null;
      $user->fin_expulsion = null;
      $user->causa_expulsion = null;
      $user->save();

    }else{

      //HOY corresponde a fin de expulsión
      //Tiempo_expulsión corresponde al tipo de expulsión

      abort_if($user->rol != 'Usuario', 420, 'No puedes expulsar a un moderador o administrador');

        $request->validate([
          'causa' => ['required','max:255'],
          'tiempo_expulsion' => ['required','string'],
          'hoy' => ['required', 'size:10'],
          ],[
            'causa.required' => 'La causa de la expulsión no puede estar vacía',
            'causa.max' => 'La causa de la expulsión tener más de 255 caracteres',
            'tiempo_expulsion.required' => 'El tipo de expulsión no puede estar vacío',
            'tiempo_expulsion.string' => 'El tipo de expulsión debe ser una cadena',
            'hoy.required' => 'El campo fin de expulsión es obligatorio',
            'hoy.size' => 'El fin de la expulsión solo puede tener 10 caracteres',
          ]);

        $user->estado = 'Expulsado';
        $user->fin_expulsion = $request->hoy;
        $user->causa_expulsion = $request->causa;
        $user->save();

        $expulsion = new Expulsion([
          'user_id' => $request->user_id,
          'causa' =>  $request->causa,
          'tipo_expulsion' =>  $request->tiempo_expulsion
        ]);
        $expulsion->save();

    }
  }

  public function getStatsSexoUsers(){
    return DB::table('users')->selectRaw('sexo, count(id) as cantidad')->groupBy('sexo')->get();
  }

  public function getUnban(Request $request){

    $user = $user = User::find($request->user_id);
    $user->estado = null;
    $user->fin_expulsion = null;
    $user->causa_expulsion = null;
    $user->save();

  }

  public function cambiarRol(Request $request){
    $user = User::find($request['params']['user_id']);
    if($request['params']['rol'] == 'Usuario'){
      $user->rol = $request['params']['rol'];
    }else {
      $user->rol = $request['params']['rol'];
    }
      $user->save();
  }

  public function verify($code){
    $user = User::where('confirmation_code', $code)->first();

    if (! $user)
        return redirect('/');

    $user->confirmed = true;
    $user->confirmation_code = null;
    $user->save();

    return redirect('/home')->with('notification', 'Has confirmado correctamente tu correo!');
}

}
