<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('store_name')->unique();
            $table->string('store_type');
            $table->string('store_update_image');
            $table->integer('customer_id')->unique();
            $table->integer('product_id');


            $table->integer('sales')->default(0);
            $table->integer('order_id')->default(0);
            $table->double('views')->default(0);
            $table->double('visitor')->default(0);



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
        Schema::dropIfExists('stores');
    }
}
