<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\Apple\AppleExtendSocialite::class.'@handle',
        ],
        'App\Events\UpdatePriceEvent' => [
            'App\Listeners\UpdatePriceListener',
        ],
        'App\Events\ProductEvent' => [
            'App\Listeners\ProductListener',
        ],
        'App\Events\LiveStoreProductEvent' => [
            'App\Listeners\LiveStoreProductListener',
        ],
        'App\Events\WinnerParticipantEvent' => [
            'App\Listeners\WinnerParticipantListener',
        ],
    ];
}