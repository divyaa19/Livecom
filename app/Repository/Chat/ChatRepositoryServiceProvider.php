<?php

/**
 * ServiceProvider : ChatRepositoryServiceProvider.
 *
 *
 * @author lukman
 */

namespace App\Repository\Chat;

use App\Models\ChatRoomOwner;
use Illuminate\Support\ServiceProvider;

class ChatRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registers with Laravels IoC Container.
     */
    public function register()
    {
        $this->app->bind(
            ChatInterface::class,
            function ($app){
                return new ChatImpl(new ChatRoomOwner());
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
