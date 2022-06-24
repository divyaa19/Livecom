<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\Live;

class LiveReady extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:ready';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an notification to remind seller ready to live';

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
        $today  = Carbon::today('Asia/Kuala_Lumpur');
        $now = Carbon::now()->format('Y-m-d H:i');
        $expire_time = Carbon::now()->addDays(1);
        $today  = Carbon::today('Asia/Kuala_Lumpur');

        $subhours = Carbon::now()->subhours(1);

        $lives = Live::where('start_date',$today)
                        ->where('start_time',$now)
                        ->get();


        foreach($lives as $live){
            
            $time = Carbon::parse($live['start_time'])->format('Y-m-d H:i');

            if($time == $now){

                Log::info("Notification Sent");

                DB::table('oc_notifications')->insert([
                    'customer_id' => 0,
                    'from_customer_id' => 0,
                    'notification_title' => "It's time to go live !",
                    'notification_message' => "It's time to go live !",
                    'notification_interaction' => "It's time to go live !",
                    'notification_action' => "It's time to go live !",
                    'notification_datetime' => $now,
                    'notification_expire_datetime' => $expire_time,
                    'unique_id' => $live['unique_id'],
                    'from_unique_id' => 0,
                    'type' => 'buying_modes'
                   ]);

            }
        }

        Log::info("Cron is working fine!");
    }
}
