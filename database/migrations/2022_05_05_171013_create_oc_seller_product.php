<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcSellerProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_seller_product', function (Blueprint $table) {
            $table->id();
            $table->string('product_title');
            $table->string('product_category');
            $table->string('product_description');
            $table->string('product_code')->nullable();
            $table->integer('price');
            $table->integer('stock');
            $table->integer('selling_mode_id');
            $table->integer('buying_mode_id');
            $table->integer('bid_amount')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('starting_price')->nullable();
            $table->integer('final_price')->nullable();
            $table->time('game_duration')->nullable();
            $table->text('cover_image');
            $table->text('image');
            $table->text('image_2')->nullable();
            $table->text('image_3')->nullable();
            $table->text('image_4')->nullable();
            $table->text('image_5')->nullable();
            $table->text('image_6')->nullable();
            $table->string('seller_unique_id');
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
        Schema::dropIfExists('oc_seller_product');
    }
}
