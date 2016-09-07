<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('weekday');
            $table->integer('startHour');
            $table->integer('startMinutes');
            $table->integer('endHour');
            $table->integer('endMinutes');
            $table->integer('user_asset_id')->unsigned()->nullable();
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
        Schema::drop('time_ranges');
    }
}
