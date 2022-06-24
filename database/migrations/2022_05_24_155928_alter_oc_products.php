<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_product', function (Blueprint $table) {
            $table->string('sell_mode')->nullable()->change();
            $table->string('buy_mode')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->integer('category')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->integer('stock')->nullable()->change();
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
