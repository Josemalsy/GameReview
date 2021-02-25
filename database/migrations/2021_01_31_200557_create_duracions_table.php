<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDuracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duracions', function (Blueprint $table) {
            $table->id();
            $table->integer("game_id");
            $table->integer("user_id");
            $table->integer("juegoBase");
            $table->integer("juegoExtendido");
            $table->integer("completadoTotalmente");
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
        Schema::dropIfExists('duracions');
    }
}
