<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('oc_seller_promotions');

        Schema::create('oc_seller_promotions', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');

            $table->integer('product_id');
            $table->string('status');


            $table->integer('discount_id');
            $table->integer('set_private');
            $table->string('promotion_name');
            $table->string('promotion_code');



            $table->string('start_date');
            $table->string('end_date');
            $table->string('unit_limitation');
            $table->string('voucher_limitation');
            $table->string('minimum_spend');
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
        Schema::dropIfExists('promotions');
    }
}