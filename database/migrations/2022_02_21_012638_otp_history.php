<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OtpHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('otp_history');
        Schema::create('otp_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number');
            $table->string('country_code');
            $table->string('otp');
            $table->string('type');
            $table->string('status');
            $table->string('session_id');
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
        Schema::table('otp_history', function (Blueprint $table) {
            //
        });
    }
}