<?php
namespace App\Helpers;

use \Firebase\JWT\JWT;
use Iluminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{
    //Se necesita una key para el token
    public $key;
    public function __construct (){
        $this->key='3sT0_e5-Un4-C14v3_53cREt4';
    }

    public function signup ($email, $password, $getToken=null){
        // Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email'=>$email,
            'password'=>$password
        ])->first();
        // Comprobar si son correctos
        $signup = false; //Por defecto la autenticacion siempre tiene que ser false, o si no se lia
        if(is_object($user)){
            $signup = true; //Solo es true si user es un objeto
        }
        // Generar el token con los datos
        if($signup){
            /* Campo sub siempre hace referencia al ID del usuario
                Campo iat significa la fecha de creacion del token
                Campo exp significa la fecha de expirar del token (el tiempo es una semana)*/
            $token = array(
                'sub'   => $user->id,
                'email' => $user->email,
                'name'  => $user->name,
                'surname'  => $user->surname,
                'iat' => time(),
                'exp' => time() + (7*24*60*60)
            );
            //https://github.com/firebase/php-jwt
            $jwt = JWT::encode($token, $this->key, 'HS256');//utiliza la libreria para generar el token
            // Devolver los datos decodificados o el token, en funcion de un parametro
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'El inicio de sesion no se ha completado',
            );
        }

        return $data;
    }

    public function checkToken ($jwt, $getIdentity=false){
        $auth = false;

        try {
            $jwt = str_replace('"','',$jwt);
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if(!empty($decoded)&&is_object($decoded)&&isset($decoded->sub)){
            $auth = true;
        }else {
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }

        return $auth;
    }

}






