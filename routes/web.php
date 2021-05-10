<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Rutas del api usuario
Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);//Se puede poner desde dentro sin problema xd
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');
//Rutas del api categorias
Route::resource('/api/category','CategoryController');
//Rutas del api posts
Route::resource('/api/post','PostController');
Route::post('/api/post/upload', 'PostController@upload')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);//Se puede poner desde dentro sin problema xd
Route::get('/api/post/category/{id}', 'PostController@getPostsByCategory');
Route::get('/api/post/user/{id}', 'PostController@getPostsByUser');
//Route::post('/api/upload',['middleware'=>'api.auth'], 'UserController@upload');//cuando tiene callback


//Rutas de prueba
/* Route::get('/test/1', 'UserController@pruebas');
Route::get('/test/2', 'CategoryController@pruebas');
Route::get('/test/3', 'PostController@pruebas'); */
