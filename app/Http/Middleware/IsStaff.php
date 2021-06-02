<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsStaff
{
  public function handle($request, Closure $next)
    {

			if (!Auth::guest()) {
				$user = Auth::user()->estado;
				if ($user == 'Expulsado') {
					return redirect('/baneo');
				}else if(Auth::user()->rol == 'Usuario'){
					return redirect('/forbidden');
				}else{
					return $next($request);
				}
			} else {
				dd("hola");
					return redirect('/login');
			}
    }
}
