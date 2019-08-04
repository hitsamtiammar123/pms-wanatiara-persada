<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class APIAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        return $this->authenticate($request, $guards)?$next($request):$this->errorResponse();
    }

    protected function errorResponse(){
        $m='Anda tidak memiliki autentikasi untuk mengakses halaman ini';
        return send_403_error($m);
    }

    protected function authenticate($request, array $guards){


        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
        return null;
    }
}
