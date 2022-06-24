<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcOrderCartListV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_order_cart_list', function (Blueprint $table) {
            $table->integer('order_id')->nullable()->change();
            $table->string('variation_1')->nullable()->after('order_status');
            $table->string('variation_2')->nullable()->after('variation_1');
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
