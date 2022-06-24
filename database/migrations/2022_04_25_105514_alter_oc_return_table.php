<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_return', function (Blueprint $table) {
            $table->string("order_id");
            $table->text("comment");
            $table->integer('order_cart_id')->after('order_id');
            $table->dateTime('date_ordered');
            $table->string('image',255)->after('comment');
            $table->string('image_2',255)->nullable()->default(null)->after('image');
            $table->string('image_3',255)->nullable()->default(null)->after('image_2');
            $table->string('image_4',255)->nullable()->default(null)->after('image_3');
            $table->string('image_5',255)->nullable()->default(null)->after('image_4');
            $table->string('image_6',255)->nullable()->default(null)->after('image_5');
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
