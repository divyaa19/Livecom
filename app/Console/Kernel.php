<?php

namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DeleteCart::class,
        Commands\EndPromotion::class,
        Commands\StartPromotion::class,
        Commands\RestockProduct::class,
        Commands\LiveNotification::class,
        Commands\LiveReady::class,
        Commands\OtherSellerLive::class,
        Commands\LiveNotification15::class,
        Commands\LiveNotification5::class,
        Commands\CartOutOfStock::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('delete:cart')->everyMinute();
        $schedule->command('end:promotion')->everyMinute();
        $schedule->command('start:promotion')->everyMinute();
        $schedule->command('restock:product')->everyMinute();
        $schedule->command('live:notification')->everyMinute();
        $schedule->command('live:notification15')->everyMinute();
        $schedule->command('live:notification5')->everyMinute();
        $schedule->command('live:schedule')->everyMinute();
        $schedule->command('live:ready')->everyMinute();
        $schedule->command('cart:outofstock')->everyMinute();
    }

}
