<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;

class LiveStoreProductEvent extends Event implements ShouldBroadcast
{
   
    public $product;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        //
        $this->product = $product;
       
    }

    public function broadcastOn()
    {
       return new Channel('product-live');
      
    }
    public function broadcastAs()
    {
        return 'product-live-info';
    }
}