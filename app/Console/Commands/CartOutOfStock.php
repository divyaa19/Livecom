<?php

namespace App\Console\Commands;

use App\Models\OrderCartList;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartOutOfStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:outofstock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock in cart user is empty';

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

        Log::info("Cron is working fine");

        $products = Product::where('stock', 0)->where('status', 2)->get();

        // $product_cart = OrderCartList::where('product_id', $products->product_id);

        // $customer = OrderCartList::where('customer_id', $product_cart->customer_id);

        foreach($products as $product){

        $product_cart = OrderCartList::where('product_id', $product['product_id'])->first();

        $customer = OrderCartList::where('customer_id', $product_cart['customer_id'])->first();


            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'notification_title' => 'Your cart item is out of stock',
                'notification_message' => 'Your cart item is out of stock',
                'notification_interaction' => 'Your cart item is out of stock',
                'notification_action' => 'cart',
                'notification_datetime' => $date_time,
                'notification_expire_datetime' => $expire_time,
                'unique_id' => $customer->customer_id,
                'from_unique_id' => 0,
                'type' => 'activities',
               ]);

        }
    }
}
