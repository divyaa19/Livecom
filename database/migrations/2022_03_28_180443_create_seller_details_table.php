<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_details', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name');
            $table->string('telephone');
            $table->string('identification_no');
            $table->string('state');
            $table->string('city');
            $table->integer('store_id');
            $table->integer('customer_id');


            $table->string('postcode');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('default_address');
            $table->string('SSM')->nullable(); 
            $table->string('front')->nullable(); 
            $table->string('back')->nullable(); 
            $table->string('profile_image')->nullable(); 
            $table->string('document')->nullable(); 
            $table->string('label')->nullable(); 

           




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
        Schema::dropIfExists('seller_details');
    }
}
