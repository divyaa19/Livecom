<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Notifications;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Log;
use DB;

class RestockProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restock:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock if stock is empty';

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
        $date_time = Carbon::now();

        $expire_time = Carbon::now()->addDays(1);

        Log::info("Cron is working fine!");

        $products = Product::where('stock', 0)->where('status', 1)->get();

        foreach($products as $product){

            $store_id = $product['store_id'];

            $product_image = ProductImage::where('product_id', $product['product_id'])->first('image');

            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'notification_title' => 'Restock this item'.' product_id: '.$product['product_id'],
                'notification_message' => 'Restock this item',
                'notification_interaction' => 'Restock this item',
                'notification_action' => 'Restock this item',
                'notification_datetime' => $date_time,
                'notification_expire_datetime' => $expire_time,
                'unique_id' => $store_id,
                'from_unique_id' => 0,
                'type' => 'activities',
                'profile_image' => $product_image,
               ]);
        }        
    }
}
