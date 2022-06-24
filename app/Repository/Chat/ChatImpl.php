<?php


namespace App\Repository\Chat;

use App\Models\ChatRoomOwner;
use Illuminate\Database\Eloquent\Model;

class ChatImpl implements ChatInterface
{
    protected Model $chatroomOwner;

    public function __construct(ChatRoomOwner $chatRoomOwner)
    {
        $this->chatroomOwner = $chatRoomOwner;
    }

    public function getChatList(string $user_id)
    {
        return ChatRoomOwner::with('chat', 'user', 'seller')
            ->where('user_unique_id', $user_id)
            ->orderby('id','desc')
            ->get();
    }
}
