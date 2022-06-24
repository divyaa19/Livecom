<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcReviewAddRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_review', function (Blueprint $table) {
            $table->string("text");
            $table->integer("rating");
            $table->string('image',255)->after('text');
            $table->string('image_2',255)->nullable()->default(null)->after('image');
            $table->string('image_3',255)->nullable()->default(null)->after('image_2');
            $table->string('image_4',255)->nullable()->default(null)->after('image_3');
            $table->string('image_5',255)->nullable()->default(null)->after('image_4');
            $table->string('image_6',255)->nullable()->default(null)->after('image_5');
            $table->integer('seller_service')->default(0)->after('rating');
            $table->integer('delivery_service')->default(0)->after('seller_service');
            $table->integer('product_quality')->default(0)->after('delivery_service');
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
