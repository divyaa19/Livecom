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

class WinnerParticipantEvent extends Event implements ShouldBroadcast
{
   
    public $toscorer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LiveStores $toscorer)
    {
        //
        $this->toscorer = $toscorer;
       
    }

    public function broadcastOn()
    {
       return new Channel('product-winner');
      
    }
    public function broadcastAs()
    {
        return 'participant-winner';
    }
}