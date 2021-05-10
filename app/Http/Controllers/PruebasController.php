<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PruebasController extends Controller
{
    public function testOrm (){

        $posts = Post::all();
        /* var_dump($posts); */

        /* foreach($posts as $post){
            echo "<h1>".$post-> title ."<br></h1>";
            echo "<span style='color:gray'>Creado por: {$post->user->name} {$post->user->surname} {$post->user->created_at}</span><br>";
            echo "<span style='color:red'>Lenguaje: {$post->category->name}</span>";
            echo "<p>".$post-> content ."</p>";
            echo "<hr>";
        } */

        $categories = Category::all();
        /* var_dump($posts); */

        foreach($categories as $category){
            echo "<span style='color:gray'>Categoria: {$category->name} </span><br>";
            foreach($category->posts as $post){
                echo "<h1>".$post-> title ."<br></h1>";
                echo "<span style='color:gray'>Creado por: {$post->user->name} {$post->user->surname} {$post->user->created_at}</span><br>";
                echo "<p>".$post-> content ."</p>";
            }
            echo "<hr>";
        }

        die();
    }
}
