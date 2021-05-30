<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsStaff
{
  public function handle($request, Closure $next)
    {
        if (!Auth::guest()) {
            $user = Auth::user()->rol;
            if ($user == 'Usuario') {

                return redirect('/forbidden');
            }
            return $next($request);
        } else {
            return redirect('/login');
        }
    }
}
