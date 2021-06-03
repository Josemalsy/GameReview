<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsLoging
{
  public function handle($request, Closure $next)
    {

      if(!Auth::guest()){
        $user = Auth::user()->estado;
        if($user == 'Expulsado'){
          return redirect('/baneo');
        }else{
          return $next($request);
        }
      }else{
        return redirect('/login');
      }
    }
}
