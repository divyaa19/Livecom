<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcMyCart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_my_cart', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('variation_color')->nullable();
            $table->string('variation_size')->nullable();
            $table->string('product_name');
            $table->string('product_image');
            $table->integer('quantity');
            $table->string('type');
            $table->string('total');
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
        Schema::dropIfExists('oc_my_cart');
    }
}
