<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOcSellerPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_seller_promotions', function (Blueprint $table) {
            $table->string('promotion_type')->after('set_private');
            $table->integer('active_immediately')->default(0)->after('minimum_spend');
            $table->string('promotion_code')->nullable()->change();
            $table->string('voucher_limitation')->nullable()->change();
            $table->string('minimum_spend')->nullable()->change();
            $table->integer('status')->default(1)->change();
            $table->integer('set_private')->default(0)->change();
            $table->string('seller_id')->change();
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
