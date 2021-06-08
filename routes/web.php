<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GameController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\facades\Mail;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/prueba', function () {
	return 9;
})->middleware(['auth','verified']);

Route::get('/email/verify', function () {
	return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
	$request->fulfill();

	return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');


Route::group(['middleware' => 'IsGuest'], function() {

	Route::get('/mensajes/leer_mensaje', function () {
		return view('welcome');
	});

	Route::get('/mensajes', function () {
		return view('welcome');
	});

	Route::get('/usuario/{id}/reviews', function () {
		return view('welcome');
	})->middleware(['auth','verified']);

	Route::get('/usuario/{id}/juegos', function () {
		return view('welcome');
	})->middleware(['auth','verified']);

	Route::get('/usuario/{id}', function () {
		return view('welcome');
	});

	Route::get('/game/{id}', function () {
		return view('welcome');
	});


});

Route::group(['middleware' => 'IsBan'], function() {

	Route::get('/', function () {
		return view('welcome');
	});

	Route::get('/stats', function () {
		return view('welcome');
	});

	Route::get('/usuarios', function () {
		return view('welcome');
	});

});

Route::group(['middleware' => 'IsStaff'], function() {

	Route::get('/staff_tools', function() {
		return view('welcome');
	});

	Route::get('/staff_tools/listaGeneros', function() {
		return view('welcome');
	});

	Route::get('/staff_tools/listaPlataformas', function() {
		return view('welcome');
	});

	Route::get('/staff_tools/listaUsuarios', function() {
		return view('welcome');
	});

});


Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/register/verify/{code}', [App\Http\Controllers\Auth\RegisterController::class, 'verify']);

Route::get('/{any}', [App\Http\Controllers\SpaController::class, 'index'])->where('any', '^(?!api).*$');