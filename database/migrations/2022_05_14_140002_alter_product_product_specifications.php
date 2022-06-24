<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductProductSpecifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_product_specifications', function (Blueprint $table) {
            $table->string('specification_value')->after('id');
            $table->string('specification_title')->after('id');
            $table->string('specification_id')->after('id');
            $table->bigInteger('product_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_product_specifications', function (Blueprint $table) {
            $table->dropColumn('product_id');
            $table->dropColumn('specification_id');
            $table->dropColumn('specification_title');
            $table->dropColumn('specification_value');
        });
    }
}
