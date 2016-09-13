<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskassetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskassets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('needed');
            $table->integer('task_id')->unsigned()->nullable();
            $table->integer('asset_id')->unsigned()->nullable();
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
        Schema::drop('taskassets');
    }
}
