<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function register (Request $request){
        /* Se crea la variable json para recibir todos los datos por medio de json
            Utiliza el request, y le dice que recibira un objeto con el nombre de Json, con todos los campos dentro,
            si no es asi, simplemente manda nulo
        */
        $json=$request->input('json', null);
        /* Decodificacion de los datos */
        $params = json_decode($json);//devuelve objeto
        $params_array = json_decode($json,true);//devuelve array
        $params_array = array_map('trim',$params_array);//Limpia de espacios los datos
        /* Validar datos recibidos
            Se validan gracias a la libreria \Validator:make(Recibe array con losd atos a validar)
        */
        if(!empty($params_array)&&!empty($params)){
            //se le dice que el email sea unico en la tabla users y al name y surname que solo acepten letras y espacios
            $validate = \Validator::make($params_array,[
                'name'      =>'required|regex:/^[\pL\s\-]+$/u',
                'surname'   =>'required|regex:/^[\pL\s\-]+$/u',
                'email'     =>'required|email|unique:users',
                'password'  =>'required'
            ]);
            /* Solo para comprobar si funciona, usa fails() para ver si falla o no, y errors para saber que errores fueron. */
            if($validate->fails()){
                /* Validacion ha fallado */
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'message'=> 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );

            }else{
                /* Validacion pasada correctamente */
                //Cifrar la contraseña, usa bcrypt y la cifra 4 veces para que no sea tan pesado
                $pwd = hash('sha256', $params->password);
                /* Crea el usuario */
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                //Meter informacion opcional por defecto
                $user->role = 'User';

                /* Como final, guardarlo todo */
                $user->save();


                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'El usuario se ha creado correctamente',
                    'name'   => $user
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'Los datos enviador no son correctos',
            );
        }
        return response()->json($data);

    }

    public function login (Request $request){

        $jwtAuth = new \JwtAuth();//se manda a llamar con el alias y la barra invertida
        /* Recibir datos por post */
        $json=$request->input('json', null);
        $params = json_decode($json);//devuelve objeto
        $params_array = json_decode($json,true);//devuelve array
        $params_array = array_map('trim',$params_array);//Limpia de espacios los datos
        /* Validar esos datos */
        $validate = \Validator::make($params_array,[
            'email'     =>'required|email',
            'password'  =>'required'
        ]);
        /* Solo para comprobar si funciona, usa fails() para ver si falla o no, y errors para saber que errores fueron. */
        if($validate->fails()){
            /* Validacion ha fallado */
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'El usuario no se ha podido loggear',
                'errors' => $validate->errors()
            );

        }else{
            /* Cifrar la contraseña */
            $pwd = hash('sha256', $params->password);
            /* Devolver token o datos */
            $data = $jwtAuth->signup($params->email, $pwd);
            if(!empty($paramms->gettoken)){
                $data = $jwtAuth->signup($params->email, $pwd, true);
            }

        }
        return response()->json($data, 200);

        /* $email='Haydee@gmail.com';
        $password='Haydee';
        $pwd = hash('sha256', $password);
        return $jwtAuth -> signup($email, $pwd); */
        /* return "Accion de login"; */
    }

    public function update (Request $request){
        //Comprueba si esta autenticado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();//alias
        $checkToken = $jwtAuth->checkToken($token);

        /* Recoger los datos por post */
        $json=$request->input('json', null);
        $params_array = json_decode($json,true);//devuelve array
        /* $params_array = array_map('trim',$params_array);//Limpia de espacios los datos */
        if($checkToken && !empty($params_array)){
            /* Actualizar usuario */
            /* Sacar los datos del usuarios */
            $user = $jwtAuth->checkToken($token, true);
            /* Validar esos datos */
            $validate = \Validator::make($params_array,[
                'name'      =>'required|regex:/^[\pL\s\-]+$/u',
                'surname'   =>'required|regex:/^[\pL\s\-]+$/u',
                'email'     =>'required|email|unique:users,'.$user->sub
            ]);
            /* Quitar los campos que no hay que actualizar de la peticion */
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            /* Actualizar el usuario en la base de datos */
            $user_update = User::where('id',$user->sub)->update($params_array);
            /* Devolver array con el resultado */
            $data = array(
                'status' => 'success',
                'code'   => 200,
                'user'=> $params_array
            );
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'El usuario no esta autenticado',
            );
        }
        return response()->json($data,$data['code']);
    }

    public function upload (Request $request){
        //Recoger datos de la application
        $image = $request->file('file0');
        //Solo aceptar imagen
        $validate = \Validator::make($request->all(), [
            'file0'=>'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        //Subir y guardar archivos
        if(!$image || $validate->fails()){

            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'Error al subir la imagen',
            );

        }else{

            $image_name = time().$image->getClientOriginalName();
            /* En laravel la forma de guardar archivos es en discos (carptas) */
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'status' => 'success',
                'code'   => 200,
                'message'=> 'Se ha subido correctamente la imagen',
                'image'=>$image_name
            );
        }
        return response()->json($data,$data['code']);
    }

    public function getImage ($filename){
        $isset=\Storage::disk('users')->exists($filename);
        if($isset){
            $file=\Storage::disk('users')->get($filename);
            return new Response($file,200);
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'La imagen no existe',
            );
            return response()->json($data,$data['code']);
        }
    }
    public function detail($id){
        $user = User::Find($id);
        if(is_object($user)){
            $data = array(
                'status' => 'success',
                'code'   => 200,
                'message'=> 'El usuario es:',
                'user'=> $user,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'El usuario no existe',
            );
        }
        return response()->json($data,$data['code']);
    }




    /* -------------------------------------------------------------------------------------------- */
    /* public function pruebas (Request $request){
        return "Accion de pruebas de user controller";
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    } */
}
