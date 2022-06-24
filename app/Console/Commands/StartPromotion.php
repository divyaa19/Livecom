<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartPromotion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:promotion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change promotion status to start promotion';

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

        Promotion::where('status', 0)->where('start_date', '<', Carbon::now())->update(['status' => 1]);
    }
}
