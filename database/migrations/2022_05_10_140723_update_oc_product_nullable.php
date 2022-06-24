<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOcProductNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   /* public function up()
    {
        Schema::table('oc_product', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->text('guid')->nullable()->change();
            $table->text('hashid')->nullable()->change();
            // $table->string('model')->nullable()->change();
            $table->string('sku', 64)->nullable()->change();
            $table->string('upc', 12)->nullable()->change();
            $table->string('ean', 14)->nullable()->change();
            $table->string('jan', 13)->nullable()->change();
            $table->string('isbn', 17)->nullable()->change();
            $table->string('mpn', 64)->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->integer('stock_status_id')->nullable()->change();
            $table->integer('manufacturer_id')->nullable()->change();
            $table->integer('tax_class_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
    public function down()
    {
        //
    }*/
}
