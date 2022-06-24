<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductChangeSellerIdToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_product', function (Blueprint $table) {
            $table->string('store_id')->change();
            $table->string('guid')->nullable()->change();
            $table->string('hashid')->nullable()->change();
            $table->string('model')->nullable()->change();
            $table->string('sku')->nullable()->change();
            $table->string('upc')->nullable()->change();
            $table->string('ean')->nullable()->change();
            $table->string('jan')->nullable()->change();
            $table->string('isbn')->nullable()->change();
            $table->string('mpn')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('stock_status_id')->nullable()->change();
            $table->string('manufacturer_id')->nullable()->change();
            $table->string('tax_class_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_product', function (Blueprint $table) {
            $table->integer('store_id')->change();
        });
    }
}
