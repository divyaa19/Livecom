<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product', function (Blueprint $table) {
            $table->id();
            $table->string('store_id');
            $table->string('guid')->nullable();
            $table->string('hashid')->nullable();
            $table->string('model')->nullable();
            $table->string('sku')->nullable();
            $table->string('upc')->nullable();
            $table->string('ean')->nullable();
            $table->string('jan')->nullable();
            $table->string('isbn')->nullable();
            $table->string('mpn')->nullable();
            $table->string('location')->nullable();
            $table->string('stock_status_id')->nullable();
            $table->string('manufacturer_id')->nullable();
            $table->string('tax_class_id')->nullable();
            $table->string('sell_mode')->nullable();
            $table->string('buy_mode')->nullable();
            $table->string('title')->nullable();
            $table->integer('category')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('stock')->nullable();
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
        Schema::dropIfExists('oc_product');
    }
}
