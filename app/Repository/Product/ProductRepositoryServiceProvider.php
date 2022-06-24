<?php

/**
 * ServiceProvider : ChatRepositoryServiceProvider.
 *
 *
 * @author lukman
 */

namespace App\Repository\Product;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;

class ProductRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registers with Laravels IoC Container.
     */
    public function register()
    {
        $this->app->bind(
            ProductInterface::class,
            function ($app) {
                return new ProductImpl(new Product());
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
