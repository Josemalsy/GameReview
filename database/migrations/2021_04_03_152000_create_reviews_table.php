<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreign('game_id')->references('id')->on('games');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('juegoBase');
            $table->integer('juegoExtendido')->nullable();
            $table->integer('completadoTotal')->nullable();
            $table->text('mensaje');
            $table->integer('puntuacion');
            $table->string('estado')->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->string('visto')->default('No');
            $table->string('borrado_receptor');
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
        Schema::dropIfExists('reviews');
    }
}