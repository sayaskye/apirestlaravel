<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');/* Nombre del usuario */
            $table->string('surname');/* Apellido del usuario */
            $table->string('role')->nullable();/* Rol en la pagina (admin solo 1) */
            $table->string('email')->unique(); /* Email del usuario, tiene que ser unico */
            $table->string('password');/* Contraseña del usuario */
            $table->text('description')->nullable();/* Descripcion del perfil del usuario */
            $table->string('image')->nullable();/* Imagen de perfil de usuario */
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
