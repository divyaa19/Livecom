<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcRefund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_refund', function (Blueprint $table) {
            $table->id();
            $table->integer('order_cancel_id');
            $table->integer('return_id');
            $table->decimal('refund_amount', 10, 2);
            $table->string('status')->default(0);
            $table->timestamp('paid_at')->nullable()->default(null);
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
        Schema::dropIfExists('oc_refund');
    }
}
