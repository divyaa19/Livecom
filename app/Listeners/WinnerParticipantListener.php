<?php

namespace App\Listeners;

use App\Events\WinnerParticipantEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class WinnerParticipantListener implements ShouldQueue
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
    public function handle(WinnerParticipantEvent $event)
    {
        
       return $event;
    }
}
