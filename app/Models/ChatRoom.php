<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoom extends Model
{
    protected $table = 'oc_chat_room';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'created_by'
    ];

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(ChatRoomOwner::class, 'chatroom_id', 'id');
    }
}
