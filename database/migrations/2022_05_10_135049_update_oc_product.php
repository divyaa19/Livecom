<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOcProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_product', function (Blueprint $table) {
            $table->integer("product_id");
            $table->string("price");
            $table->string('sell_mode')->after('product_id');
            $table->string('buy_mode')->after('sell_mode');
            $table->string('title')->after('buy_mode');
            $table->tinyInteger('category')->after('title');
            $table->text('description')->after('category');
            $table->string('code')->nullable()->after('description');
            $table->integer('stock')->after('price');
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
