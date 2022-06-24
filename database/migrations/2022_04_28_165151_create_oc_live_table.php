<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcLiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_live', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('is_schedule');
            $table->string('title');
            $table->string('thumbnail');
            $table->string('type');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->string('pin_message')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('product_title')->nullable();
            $table->float('selling_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oc_live');
    }
}
