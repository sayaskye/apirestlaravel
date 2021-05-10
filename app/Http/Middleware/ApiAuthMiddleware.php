<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Comprueba si esta autenticado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();//alias
        $checkToken = $jwtAuth->checkToken($token);
        if($checkToken){
            return $next($request);
        }
        else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'El usuario no esta autenticado, por favor inicia sesion',
            );
            return response()->json($data,$data['code']);
        }
    }
}
