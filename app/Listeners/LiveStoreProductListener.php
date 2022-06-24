<?php

namespace App\Listeners;

use App\Events\LiveStoreProductEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class LiveStoreProductListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdatePriceEvent  $event
     * @return void
     */
    public function handle(LiveStoreProductEvent $event)
    {
        
       return $event;
    }
}
