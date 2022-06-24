<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcLivestreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_livestreams', function (Blueprint $table) {
            $table->increments('stream_id');
            $table->integer('seller_id');
            $table->integer('customer_id');
            $table->string('description');
            $table->integer('product_id');
            $table->integer('mode_id');

            $table->double('starting_price');
            $table->double('final_price');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');


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
        Schema::dropIfExists('oc_livestreams');
    }
}