<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeliculasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('director');
            $table->integer('duracion');
	        $table->string('genero');
            $table->text('reparto');
	        $table->text('sinopsis');
	        $table->string('clasificacion');
            $table->date('fechaEstreno');
            $table->string('tipo_pelicula');
            $table->text('imagen_promocional');
            $table->text('trailer');

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
        Schema::dropIfExists('peliculas');
    }
}