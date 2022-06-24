<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('value');
            $table->string('price');
            $table->string('stock');
            $table->string('variation_title');
            $table->string('variation_size');

           
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
        Schema::dropIfExists('oc_product_variations');
    }
}