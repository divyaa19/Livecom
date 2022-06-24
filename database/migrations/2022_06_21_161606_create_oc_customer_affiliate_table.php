<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcCustomerAffiliateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_customer_affiliate', function (Blueprint $table) {
            $table->id();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('tracking')->nullable();
            $table->string('commission')->nullable();
            $table->string('tax')->nullable();
            $table->string('payment')->nullable();
            $table->string('cheque')->nullable();
            $table->string('paypal')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_branch_number')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('custom_field')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('oc_customer_affiliate');
    }
}
