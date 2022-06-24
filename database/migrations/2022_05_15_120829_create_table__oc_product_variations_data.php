<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOcProductVariationsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product_variations_data', function (Blueprint $table) {
            $table->id();
            $table->integer('variation_id');
            $table->string('type');
            $table->string('variation');
            $table->double('variation_price');
            $table->integer('variation_stock');
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
        Schema::dropIfExists('oc_product_variations_data');
    }
}
