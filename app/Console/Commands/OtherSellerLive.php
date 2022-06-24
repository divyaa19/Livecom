<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Follow;
use App\Models\Live;
use App\Models\PurpleTreeStore;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class OtherSellerLive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send live notifications to followed seller';

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
        $now = Carbon::now()->format('Y-m-d H:i');
        $expire_time = Carbon::now()->addDays(1);
        $today  = Carbon::today('Asia/Kuala_Lumpur');

        $lives = Live::where('start_date', $today)
                        ->where('start_time', $now)
                        ->where('is_schedule', 'yes')
                        ->get();

        foreach($lives as $data){

            $time = Carbon::parse($data['start_time'])->format('Y-m-d H:i');

            $unique_id = $data['unique_id'];

            $follow = Follow::where('unique_id', $unique_id)->get();

            if($time == $now){

                Log::info("Notification Sent to follower");

                foreach($follow as $follower){

                $Username = Customer::where('user_id', $follower['unique_id'])->first('username');

                $get_user = $unique_id;
                $explode_user = explode("_", $get_user);

                if($explode_user[0] == "ST"){

                    $user = PurpleTreeStore::where('seller_unique_id', $unique_id)
                        ->first();

                    $name = $user['store_name'];

                    $profile_image = $user['store_logo'];

                }
                elseif($explode_user[0] == "CS"){

                    $user = Customer::where('user_id', $unique_id)
                                ->first();

                    $name = $user['username'];

                    $profile_image = $user['profile_url'];
                }

                $user_name = $name;
                $user_image = $profile_image;


                    DB::table('oc_notifications')->insert([
                        'customer_id' => 0,
                        'from_customer_id' => 0,
                        'notification_title' => $user_name. " is on live now",
                        'notification_message' => $user_name. " is on live now",
                        'notification_interaction' => " is on live now",
                        'notification_action' => "live",
                        'notification_datetime' => $now,
                        'notification_expire_datetime' => $expire_time,
                        'unique_id' => $follower['by_unique_id'],
                        'from_unique_id' => $unique_id,
                        'type' => 'socials',
                        'name' => $user_name,
                        'profile_image' => $user_image
                    ]);
                }

            }
        }
    }
}
