<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\LiveStores;

class TopParticipantsEvent extends Event implements ShouldBroadcast
{
   
    public $topbit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($topbit)
    {
        //
        $this->topbit = $topbit;
       
    }

    public function broadcastOn()
    {
       return new Channel('bit');
      
    }
    public function broadcastAs()
    {
        return 'top-bit';
    }
}