<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('discounts');
        
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->string('discount_type');
            $table->string('discount_amount');
            $table->string('percentage_off');
            $table->integer('product_id');

            
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
        Schema::dropIfExists('discounts');
    }
}