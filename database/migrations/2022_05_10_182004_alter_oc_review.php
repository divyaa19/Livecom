<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_review', function (Blueprint $table) {
            $table->float('overall_rating');
        });

        Schema::table('oc_review', function (Blueprint $table) {
            $table->float('overall_rating')->change();
            $table->float('seller_service')->change();
            $table->float('product_quality')->change();
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
