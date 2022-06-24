<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOcProductVariations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_product_variations', function (Blueprint $table) {
            $table->dropColumn('variation_size');
            $table->dropColumn('value');
            $table->dropColumn('price');
            $table->dropColumn('stock');
            $table->integer('variation_id')->after('id');
            $table->string('variation_value')->after('variation_title');
            $table->double('variation_price')->after('variation_value');
            $table->integer('variation_stock')->after('variation_price');
            
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
