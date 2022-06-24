<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Models\OrderCartList;
use Carbon\Carbon;

class DeleteCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:cart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cart item after 24 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Cron is working fine!");

        OrderCartList::where('created_at', '<', Carbon::now()->subDays(1))->where('order_status',20)->each(function ($cart) {
            $cart->delete();
        });
    }
}
