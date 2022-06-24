<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcProductSpecifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product_specifications', function (Blueprint $table) {
            $table->id();
            $table->string('colour')->nullable();
            $table->string('brand')->nullable();
            $table->string('material')->nullable();
            $table->string('pattern')->nullable();
            $table->string('power_consumption')->nullable();
            $table->string('dimension')->nullable();
            $table->string('warranty_duration')->nullable();
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
        Schema::dropIfExists('oc_product_specifications');
    }
}
