<?php


namespace App\Repository\Chat;

interface ChatInterface
{
    /**
     * @return  chatlist
     */
    public function getChatList(string $user_id);
}
