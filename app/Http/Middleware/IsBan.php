<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsBan
{

    public function handle(Request $request, Closure $next)
    {
			if (!Auth::guest()) {
				$user = Auth::user()->estado;
				if ($user == 'Expulsado') {
					return redirect('/baneo');
				}else{
					return $next($request);
				}
			} else {
				return $next($request);
			}
    }
}
