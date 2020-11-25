<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTroublesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('troubles', function (Blueprint $table) {
            $table->integer('id_cause')->unsigned()->nullable();
            $table->string('detail')->nullable();
            $table->boolean('risk')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('troubles', function (Blueprint $table) {
            $table->dropColumn('id_cause');
            $table->dropColumn('detail');
            $table->dropColumn('risk');
        });
    }
}
