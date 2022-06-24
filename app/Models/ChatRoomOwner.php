<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomOwner extends Model
{

    protected $table = 'oc_chat_room_owner';

    protected $fillable = [
        'chatroom_id',
        'user_unique_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatRoom::class, 'id', 'chatroom_id');
    }

    public function chat(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ChatRoom::class, 'id', 'chatroom_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class, 'user_id', 'user_unique_id');
    }

    public function seller(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Store::class, 'seller_unique_id', 'user_unique_id');
    }
}
