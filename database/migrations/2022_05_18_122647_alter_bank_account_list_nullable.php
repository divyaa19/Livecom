<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBankAccountListNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_customer_affiliate', function (Blueprint $table) {
            $table->string('company')->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->string('tracking')->nullable()->change();
            $table->string('commission')->nullable()->change();
            $table->string('tax')->nullable()->change();
            $table->string('payment')->nullable()->change();
            $table->string('cheque')->nullable()->change();
            $table->string('paypal')->nullable()->change();
            $table->string('bank_name')->nullable()->change();
            $table->string('bank_account_name')->nullable()->change();
            $table->string('bank_branch_number')->nullable()->change();
            $table->string('bank_swift_code')->nullable()->change();
            $table->string('custom_field')->nullable()->change();
            $table->integer('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_customer_affiliate', function (Blueprint $table) {
            //
        });
    }
}
