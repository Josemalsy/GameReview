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
			$table->id();
			$table->string('name')->unique();
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->date('FecNac');
			$table->string('sexo');
			$table->string('ocupacion')->nullable();
			$table->string('ubicacion')->nullable();
			$table->string('aficiones')->nullable();
			$table->text('biografia')->nullable();
			$table->string('rol')->default('Usuario');
			$table->string('avatar')->default('fotos/indice.png');
			$table->string('estado');
			$table->date('fin_expulsion');
			$table->string('causa_expulsion');
			$table->string('confirmation_code')->nullable();
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
