<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcOrderCartList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_order_cart_list', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('price');
            $table->integer('discount_price');
            $table->string('store_name');
            $table->string('type');
            $table->integer('quantity');
            $table->integer('customer_id');
            $table->integer('order_id');
            $table->integer('shipping');
            $table->integer('order_status');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancellation_time')->nullable();
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
        Schema::dropIfExists('oc_order_cart_list');
    }
}
