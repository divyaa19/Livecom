<?php

namespace App\Listeners;

use App\Events\ProductEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class ProductListener implements ShouldQueue
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
    public function handle(ProductEvent $event)
    {
        
       return $event;
    }
}
