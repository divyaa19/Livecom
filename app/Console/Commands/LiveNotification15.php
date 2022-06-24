<?php

namespace App\Console\Commands;

use App\Models\Live;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LiveNotification15 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:notification15';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an notification 15 minutes before live start';

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

        $subhours = Carbon::now()->subMinutes(15);

        $lives = Live::where('start_date',$today)
                        ->where('start_time','>',$now)
                        ->get();


        foreach($lives as $live){

            $start = Carbon::parse($live['start_time'])->format('g:i A');

            $time = Carbon::parse($live['start_time'])->subMinutes(15)->format('Y-m-d H:i');

            if($time == $now){

                Log::info("Notification Sent");

                DB::table('oc_notifications')->insert([
                    'customer_id' => 0,
                    'from_customer_id' => 0,
                    'notification_title' => 'Be ready to start livestreaming at'.' '.$start,
                    'notification_message' => 'Be ready to start livestreaming at'.' '.$start,
                    'notification_interaction' => 'Be ready to start livestreaming at'.' '.$start,
                    'notification_action' => 'Be ready to start livestreaming at'.' '.$start,
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
