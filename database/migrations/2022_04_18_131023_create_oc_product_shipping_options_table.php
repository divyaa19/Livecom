<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcProductShippingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product_shipping_options', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('seller_id');
            $table->integer('shipping_courier_id');
            $table->string('shipping_fees');
            $table->string('order_limit');
            $table->integer('country_id');
            $table->string('length');
            $table->string('width');
            $table->string('height');
            $table->string('weight');
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
        Schema::dropIfExists('oc_product_shipping_options');
    }
}