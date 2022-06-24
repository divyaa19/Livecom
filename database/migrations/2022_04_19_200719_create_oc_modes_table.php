<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_modes', function (Blueprint $table) {
            $table->id();
            $table->integer('selling_mode');
            $table->integer('buying_mode');
            $table->integer('live_now');

            $table->string('start_date');
            $table->string('end_date');
            $table->string('start_time');
            $table->string('end_time');





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
        Schema::dropIfExists('oc_modes');
    }
}