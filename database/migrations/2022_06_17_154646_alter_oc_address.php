<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOcAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_address', function (Blueprint $table) {
            $table->string('customer_id')->change();
            $table->string('contact_name')->after('lastname')->change();
            $table->string('state')->after('postcode')->change();
            $table->string('label')->change();
            $table->string('custom_field')->nullable()->change();
            $table->string('firstname')->nullable()->change();
            $table->string('lastname')->nullable()->change();
            $table->string('company')->nullable()->change();
            $table->integer('default')->default(0)->change();
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
