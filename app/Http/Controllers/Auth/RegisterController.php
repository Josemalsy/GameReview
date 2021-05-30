<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
Use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
Use App\Mail\MessageReceived;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = RouteServiceProvider::HOME;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
			$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
			return Validator::make($data, [

					'name' => ['required','unique:users', 'string', 'max:255'],
					'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
					'password' => ['required', 'string', 'min:8', 'confirmed'],
					'FecNac' => ['required'],
					'genero' => ['required'],
					'avatar' => ['mimes:jpg,jpeg,png,webj','max:20']
			],[
				'name.required' => 'El campo nombre es obligatorio',
				'name.unique' => 'El nombre de usuario ya está en uso',
				'name.max' => 'El nombre no puede ocupar mas de 255 caracteres',
				'email.required' => 'El campo correo electrónico es obligatorio',
				'email.unique' => 'El correo electrónico ya está en uso',
				'email.max' => 'El correo electrónico no puede ocupar mas de 255 caracteres',
				'email.email' => 'El formato de correo electrónico no es válido',
				'password.required' => 'Debe introducir una contraseña',
				'password.confirmed' => 'Las contraseñas no coinciden',
				'password.min' => 'La contraseña debe tener más de 8 caracteres',
				'FecNac.required' => 'Debe introducir una fecha de nacimiento',
				'genero.required' => 'Debe introducir su género',
				'avatar.mimes' => 'El avatar debe ser formato  PNG/JPG/JPEG/WEBJ',
				'avatar.max' => 'El avatar no puede pesar mas de 20kb',
			]);

			if(error()){
					return back()->with('error', $error);
			}
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\Models\User
	 */
	protected function create(array $data)
	{

		$imagenEnlace = ( !empty(request()->file('avatar')) ) ? request()->file('avatar')->store('fotos', 'public') : 'fotos/indice.png';

		$data['confirmation_code'] = Str::random(25);


		return User::create([
				'name' => $data['name'],
				'email' => $data['email'],
				'password' => Hash::make($data['password']),
				'FecNac' => $data['FecNac'],
				'sexo' =>  $data['genero'],
				'avatar' => $imagenEnlace,
				'confirmation_code' => $data['confirmation_code']
		]);

		Mail::send('emails.confirmation_code', $data , function($message) use ($data){
			$message->to($data['email'],$data['name'])->subject('Por favor confirma tu correo');
		});

		return "mensaje enviado";

	}

	public function verify($code){
		$user = User::where('confirmation_code', $code)->first();

		if(!$user){
			return redirect('/');
		}

		$user->confirmed = true;
		$user->confirmation_code = null;
		$user->save();

		return redirect('/login')->with('notification','Has confirmado correctamente tu correo');

	}

	public function show(){
		$usuario = User::all();

		return view('usuarios',compact("usuario"));
	}
}
