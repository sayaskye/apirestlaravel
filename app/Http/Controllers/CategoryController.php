<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    //Para poder utilizar el middleware en store sin afectar a las demas:
    //Usa el middleware api.auth en todos los metodos excepto en:
    public function __construct (){
        $this->middleware('api.auth',['except' =>['index','show']]);
    }

    public function index()
    {
        //All basicamente busca y trae Todos los datos que se le pidan.
        $categories = Category::all();
        return response()->json([
            'code'=>200,
            'status'=>'Success',
            'categories'=>$categories
        ]);
    }

    public function create()
    {
        //Esto es para la vista, ignorar.
    }

    public function store(Request $request)
    {
        //Recoger los datos por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);//devuelve array
        if(!empty($params_array)){
            //Validar los datos
            $validate = Validator::make($params_array,[
                'name'=>'required|unique:categories',
            ]);
            //Guardar la categoria
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'message'=> 'La categoria no ha sido creada',
                    'errors' => $validate->errors()
                );

            }else{
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();


                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'La categoria se ha creado correctamente',
                    'name'   => $category
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'No se ha podido completar la accion de crear categoria',
            );
        }
        //Devolver el resultado
        return response()->json($data,$data['code']);
    }

    public function show($id)
    {
        //Find saca del modelo por su llave primaria, en este caso el ID, la variable solo es para buscar, nada mas.
        $category = Category::find($id);
        if(is_object($category)){
            $data = [
                'code'=>200,
                'status'=>'Success',
                'category'=>$category
            ];
        }else{
            $data = [
                'code'=>400,
                'status'=>'error',
                'message'=>'La categoria no existe'
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function edit(Category $category)
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
                'name'=>'required|unique:categories',
            ]);
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'message'=> 'La categoria no ha sido actualizada',
                    'errors' => $validate->errors()
                );

            }else{
                //Quitar lo que no hay que actualizar
                unset($params_array['id']);
                unset($params_array['created_at']);
                //Actualizar la categoria
                $categorie_update = Category::where('id',$id)->update($params_array);

                $data = array(
                    'status' => 'success',
                    'code'   => 200,
                    'message'=> 'La categoria se ha actualizado correctamente',
                    'name'   => $params_array
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'message'=> 'Error con el intento de actualizar la categoria',
            );
        }
        //Devolver los datos
        return response()->json($data,$data['code']);



    }

    public function destroy($id)
    {
        //Find saca del modelo por su llave primaria, en este caso el ID, la variable solo es para buscar, nada mas.
        $category = Category::find($id);
        if(is_object($category)){
            $data = [
                'code'=>200,
                'status'=>'Categoria Eliminada correctamente',
            ];
            $category->delete();
        }else{
            $data = [
                'code'=>400,
                'status'=>'error',
                'message'=>'La categoria no existe'
            ];
        }
        return response()->json($data,$data['code']);
    }
}
