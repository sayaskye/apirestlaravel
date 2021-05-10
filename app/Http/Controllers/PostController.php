<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    //Para poder utilizar el middleware en store sin afectar a las demas:
    //Usa el middleware api.auth en todos los metodos excepto en:
    public function __construct (){
        $this->middleware('api.auth',['except' =>['index','show', 'getImage', 'getPostsByCategory', 'getPostsByUser']]);
    }
    public function index()
    {
        //All basicamente busca y trae Todos los datos que se le pidan.
        //Load permite cargar adjunto a lo demas dentro del json como un objeto anidado lo que se le pida, en este caso category
        $posts = Post::all()->load('category', 'user');
        return response()->json([
            'code'=>200,
            'status'=>'Success',
            'categories'=>$posts
        ], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //Recoger los datos del post
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);
        if(!empty($params_array)){
            //Conseguir el usuario registrado
            $user = $this->getIdentity($request);

            //Validar los datos
            $validate = Validator::make($params_array,[
                'title'             =>'required',
                'content'           =>'required',
                'category_id'       =>'required',
                'image'             =>'required',
            ]);
            //Guardar el articulo
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'message'=> 'La validacion de guardar post no ha sido correcta, verifica datos1',
                );
            }else{
                //$tiene que ser el params normal (objeto) por que si no el array no permite asi
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;

                $post->save();

                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'post'=> $post,
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'No ha sido posible enviar los datos, verifica tu metodo de envio',
            );
        }

        //Devolver la respuesta
        return response()->json($data, $data['code']);
    }

    public function show($id)
    {
        //Find saca del modelo por su llave primaria, en este caso el ID, la variable solo es para buscar, nada mas.
        $post = Post::find($id)->load('category', 'user');
        if(is_object($post)){
            $data = [
                'code'=>200,
                'status'=>'Success',
                'post'=>$post
            ];
        }else{
            $data = [
                'code'=>400,
                'status'=>'error',
                'message'=>'El post no existe'
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function edit(Post $post)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //Recoger los datos por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);
        if(!empty($params_array)){
            //Validar los datos
            $validate = Validator::make($params_array,[
                'title'             =>'required',
                'content'           =>'required',
                'category_id'       =>'required',
            ]);
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'message'=> 'El post no ha sido actualizada al no se validado',
                    'errors' => $validate->errors()
                );

            }else{
                //Quitar lo que no hay que actualizar
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);
                //quita user en caso de que exista por si acaso se le mete injeccion de codigo o algo
                unset($params_array['user']);
                //Actualizar el post
                //Conseguir el usuario registrado
                $user = $this->getIdentity($request);
                $where = [
                    'id'=>$id,
                    'user_id'=>$user->sub
                ];
                //$post = Post::where('id',$id)->updateOrCreate($params_array);
                $Post = Post::where('id',$id)->where('user_id', $user->sub)->update($params_array);
                //$post = Post::updateOrCreate($where, $params_array);

                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'El post se ha actualizado correctamente',
                    'name'   => $params_array
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'Error con el intento de actualizar El post',
            );
        }
        //Devolver los datos
        return response()->json($data,$data['code']);
    }

    public function destroy($id, Request $request)
    {
        //Conseguir el usuario registrado
        $user = $this->getIdentity($request);

        //Find saca del modelo por su llave primaria, en este caso el ID, la variable solo es para buscar, nada mas.
        //$post = Post::find($id); esto le permitiria borrar a cualquiera el post
        //where donde cumpla las condiciones, donde id sea igual al id de la url y ademas el id de registrado sea igual al id del post
        $post = Post::where('id',$id)->where('user_id', $user->sub)->first();

        if(!empty($post)&&is_object($post)){
            $data = [
                'code'=>200,
                'status'=>'El post ha sido eliminado correctamente',
                'post'=>$post
            ];
            $post->delete();
        }else{
            $data = [
                'code'=>400,
                'status'=>'error',
                'message'=>'Este post no existe no existe o no tienes los derechos para borrarlo'
            ];
        }
        return response()->json($data,$data['code']);
    }

    private function getIdentity($request){
        //Conseguir el usuario registrado
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization',null);
        //se le pasa true para que le devuelva el objeto decodificado del usuario
            //Mas info en jwtAuth.php en helpers
        $user = $jwtAuth->checkToken($token,true);
        return $user;
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
            \Storage::disk('images')->put($image_name, \File::get($image));

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
        $isset=\Storage::disk('images')->exists($filename);
        if($isset){
            $file=\Storage::disk('images')->get($filename);
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

    public function getPostsByCategory ($id){
        $posts = Post::where('category_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'posts'  => $posts
        ], 200);
    }

    public function getPostsByUser ($id){
        $posts = Post::where('user_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'posts'  => $posts
        ], 200);
    }

}
