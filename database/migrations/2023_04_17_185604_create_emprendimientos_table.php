<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emprendimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_emprendimiento',65);
            $table->text('descripcion');
            $table->tinyInteger('estado');
            $table->tinyInteger('tipo_emprendimiento');
            //$table->integer('emprendedor_id')->unsigned();
            $table->timestamps();//fecha creacion y modificacion
           //$table->foreign('emprendedor_id')->references('id')->on('users');
            $table->unsignedBigInteger('emprendedor_id');
            $table->foreign('emprendedor_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emprendimientos', function (Blueprint $table) {
            $table->dropForeign(['emprendedor_id']);
            $table->dropColumn('emprendedor_id');


        });
        Schema::dropIfExists('emprendimientos');
    }
};
