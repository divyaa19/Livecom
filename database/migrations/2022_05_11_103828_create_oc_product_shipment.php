<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcProductShipment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_product_shipment', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->boolean('shipment_free');
            $table->tinyInteger('shipment_courier');
            $table->string('region')->default();
            $table->double('fee')->default(0);
            $table->integer('limit')->default(0);
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
        Schema::dropIfExists('oc_product_shipment');
    }
}
