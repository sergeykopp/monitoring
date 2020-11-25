<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTroublesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('troubles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_directorate')->unsigned()->nullable();
            $table->integer('id_filial')->unsigned()->nullable();
            $table->integer('id_city')->unsigned()->nullable();
            $table->integer('id_office')->unsigned()->nullable();
            $table->integer('id_source')->unsigned()->default(1);
            $table->integer('id_service')->unsigned()->default(1);
            $table->integer('id_status')->unsigned()->default(1);
            $table->integer('id_user')->unsigned()->default(1);
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->text('description');
            $table->text('action')->nullable();
            $table->integer('incident')->nullable();
            $table->softDeletes()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('troubles');
    }
}
