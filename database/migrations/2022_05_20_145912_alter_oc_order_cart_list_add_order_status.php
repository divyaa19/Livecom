<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcOrderCartListAddOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_order_cart_list', function($table) {
            // $table->string('store_name')->after('discount_price');
            $table->string('stream_name')->after('store_name');
            // $table->string('type')->after('stream_name');
            // $table->string('quantity')->after('type');
            // $table->string('customer_id')->after('quantity');
            // $table->string('order_id')->after('customer_id');
            // $table->string('shipping')->after('order_id');
            // $table->integer('order_status')->after('shipping');
            $table->timestamp('cancelation_time')->after('order_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
