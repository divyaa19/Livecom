<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcSellerProductsNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_seller_products_news', function (Blueprint $table) {
            $table->id();
            $table->integer('metal');
            $table->string('type');
            $table->string('guid');
            $table->string('hashid');
            $table->string('model');
            $table->string('sku');
            $table->string('upc');
            $table->string('ean');
            $table->string('jan');
             $table->string('description');
            $table->string('category');
            $table->string('isbn');
            $table->string('mpn');
            $table->string('brand');
            $table->string('location');
            $table->string('image');
            $table->string('date_avaliable');
            $table->string('shipping_state');
            $table->string('unique_id');
            $table->string('date_added');
            $table->string('date_modified');
            
             $table->integer('selling_mode_id');
             $table->integer('buying_mode_id');
             $table->integer('shedule_id');
             $table->date('start_date'); 
            $table->time('start_time'); 
            $table->time('end_time'); 
            $table->date('end_date');






            $table->integer('bid_amount');
            $table->integer('quantity');
            $table->integer('stock_status_id');
            $table->integer('manufacture_id');
            $table->integer('shipping');
            $table->integer('points');
            $table->integer('tax_class_id');
            $table->integer('suubtract');
            $table->integer('minimum');
            $table->integer('sort_order');
            $table->integer('viwed');
            $table->integer('is_blocked');
            $table->bigInteger('store_id');
            $table->bigInteger('variation_id');
            $table->bigInteger('customer_id');









            $table->integer('price_extra_type');

            $table->float('price');
            $table->float('length');
            $table->float('width');
            $table->float('height');
            $table->float('weight');


            $table->integer('weight_calss_id');
            $table->integer('length_calss_id');


            $table->float('price_extra');

            






            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oc_seller_products_news');
    }
}
