<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportedUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_reported_user', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->integer('stream_id');
            $table->string('reported_by');
            $table->text('complain')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
            $table->integer('status')->default(0);
            $table->string('updater_name')->collation('utf8mb4_unicode_ci')->default(null);
            $table->integer('updater_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oc_reported_user');
    }
}
