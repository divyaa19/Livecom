<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBlockedToOcCustomerBlockedList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_customer_blocked_list', function (Blueprint $table) {
            //
            $table->integer("is_blocked")->after("blocked_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_customer_blocked_list', function (Blueprint $table) {
            //
            $table->dropColumn('is_blocked');
        });
    }
}
