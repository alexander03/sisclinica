<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableExameneshistoriaclinica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exameneshistoriaclinica', function (Blueprint $table) {
            $table->increments('id');
            $table->string('situacion',1); // S-> si      N -> no
            $table->string('lugar',1); //F -> fuera            C-> clinica
            $table->integer('historiaclinica_id')->unsigned()->nullable();
            $table->foreign('historiaclinica_id')->references('id')->on('historiaclinica')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('servicio_id')->unsigned()->nullable();
            $table->foreign('servicio_id')->references('id')->on('servicio')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('exameneshistoriaclinica');
    }
}
