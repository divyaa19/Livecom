<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChatMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_chat_message', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('chatroom_id');
            $table->string('message');
            $table->enum('type', ['text','picture','video','file']);
            $table->string('media')->nullable();
            $table->string('created_by');
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
        Schema::dropIfExists('oc_chat_message');
    }
}
