<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralLinkToOcCustomer extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('oc_customer', function (Blueprint $table) {
            $table->string('ref_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('oc_customer', function (Blueprint $table) {
            $table->dropColumn('ref_link');
        });
    }
}
