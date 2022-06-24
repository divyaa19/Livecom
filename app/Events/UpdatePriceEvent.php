<?php

namespace App\Events;

use App\Models\SellerProduct;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class UpdatePriceEvent extends Event  implements ShouldBroadcast
{
    public $latest_bid;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SellerProduct $latest_bid)
    {
        //
        $this->latest_bid = $latest_bid;

    }

    public function broadcastOn()
    {
        return new Channel('livestore.'.$product_id);
    }
}