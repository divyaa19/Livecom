<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcStreamProductBidAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_stream_product_bid_amounts', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('user_id');
            $table->integer('stream_id');
            $table->integer('quantity');
            $table->integer('winner');
            $table->integer('is_paid');
            $table->integer('is_epraise');
            $table->integer('timestamp');
            $table->integer('is_bid_fast');
            $table->decimal('amount');
            $table->decimal('deposit');




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
        Schema::dropIfExists('oc_stream_product_bid_amounts');
    }
}