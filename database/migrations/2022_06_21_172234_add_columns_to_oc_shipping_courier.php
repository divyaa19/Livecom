<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOcShippingCourier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_shipping_courier', function (Blueprint $table) {
            //
            $table->string("shipping_courier_code")->after("shipping_courier_id");
            $table->string("shipping_courier_name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_shipping_courier', function (Blueprint $table) {
            //
            $table->dropColumn("shipping_courier_code");
            $table->dropColumn("shipping_courier_name");
        });
    }
}
