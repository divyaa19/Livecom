<?php

namespace App\Http\Controllers;

use App\Models\BidType;
use Illuminate\Http\Request;
use App\Models\Live;
use App\Models\PurpleTreeStore;
use App\Models\ProductSession;
use App\Models\oc_products_new;
use App\Models\GetBidder;
use App\Models\Product;
use App\Models\OrderCartList;
use App\Models\SellerProduct;
use App\Models\Stream;
use App\Models\Participants;
use App\Models\Customer;
use App\Models\Follow;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PurpleTreeOrder;
use App\Repository\Product\ProductInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class LiveController extends Controller
{
    public ProductInterface $product;


    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getStreamProduct(Request $request, $seller_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->allStreamProduct($seller_id),
        ]);
    }

    public function getAllSellingStreams($stream_type)
    {
        $streams = DB::table('oc_live')
                        ->select('oc_live.*','oc_purpletree_vendor_stores.store_name','oc_purpletree_vendor_stores.store_logo','oc_purpletree_vendor_stores.store_banner','oc_purpletree_vendor_stores.username',)
                        ->join('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_unique_id','=','oc_live.unique_id')
                        ->get();

        return response()->json($streams);
    }

    public function getAllStreams()
    {
        $streams = Live::all();

        return response()->json($streams);
    }

    public function getAllEntertainmentStreams($stream_type)
    {
        $streams = DB::table('oc_live')
                        ->select('oc_live.*','oc_customer.firstname','oc_customer.profile_url','oc_customer.username')
                        ->join('oc_customer','oc_customer.user_id','=','oc_live.unique_id')
                        ->get();

        return response()->json($streams);
    }

    public function getStream(Request $request,$stream_type)
    {   
        $stream = Live::where('type',$stream_type)
                        ->where('unique_id',$request['unique_id'])
                        ->where('id',$request['id'])
                        ->first();

        return response()->json(['stream' => $stream,
                                 'status' => 'success',
                                 'success' => true], 200);
    }


    public function go_live(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        $user_unique_id = $user->user_id;
        $name = "";
        $profile_image = "";

        if($request['type'] == 'selling')
        {
            $seller_id = PurpleTreeStore::where('seller_id',$customer_id)->first();
            if($seller_id === null)
            {
                 $user_unique_id = 0;
            }
            else
            {
                 $user_unique_id = $seller_id->seller_unique_id;
            }
           
        }

        $this->validate($request,[
            'title' => 'required',
            'thumbnail' => 'required',
        ]);

        $title = $request['title'];
        $thumbnail = $request['thumbnail'];
        $start_date = $request['start_date'];
        $start_time = $request['start_time'];

        if(!isset($request['is_schedule'])) {
            $request['is_schedule'] = 'no';
        }

            $live = new Live;
            $live->title = $title;
            $live->unique_id = $user_unique_id;
            $live->is_schedule = $request['is_schedule'];
            $live->type = $request['type'];
            $live->start_date = Carbon::now();
            $live->start_time = Carbon::now();
            $live->thumbnail = $thumbnail;
            $live->streaming_id = $request['streaming_id'];
            $live->save();

            //Send Notifications to all followers seller
            $tomorrow = new DateTime('tomorrow');

            $get_user = $user_unique_id;
            $explode_user = explode("_", $get_user);

            if($explode_user[0] == "ST"){

                $user = PurpleTreeStore::where('seller_unique_id', $user_unique_id)
                    ->first();

                $name = $user['store_name'];

                $profile_image = $user['store_logo'];

            }
            elseif($explode_user[0] == "CS"){

                $user = Customer::where('user_id', $user_unique_id)
                            ->first();

                $name = $user['username'];

                $profile_image = $user['profile_url'];
            }

            $user_name = $name;
            $user_image = $profile_image;

            $follower = Follow::where('unique_id','=', $user_unique_id)->get();
            // dd($follower);

            foreach($follower as $data){

                DB::table('oc_notifications')->insert([
                    'customer_id' => 0,
                    'from_customer_id' => 0,
                    'type' => 'socials',
                    'notification_title' => $user_name. " is on live now",
                    'notification_message' => $user_name. " is on live now",
                    'notification_action' => 'live',
                    'notification_interaction' => '',
                    'notification_is_read' => 0,
                    'notification_datetime' => Carbon::now(),
                    'notification_expire_datetime' => $tomorrow,
                    'unique_id' => $data['by_unique_id'],
                    'from_unique_id' => $user_unique_id,
                    'name' => $user_name,
                    'profile_image' => $user_image
                ]);
            }

           

            $live_id = $live->id;

        return response()->json(['live_id' => $live_id,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function schedule_live(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        $unique_id = $user->user_id;

        if($request['type'] == 'selling')
        {
            $seller_id = PurpleTreeStore::where('seller_id', $customer_id)->first();
            if($seller_id === null)
            {
                $unique_id = 0;
            }
            else
            {
                $unique_id = $seller_id->seller_unique_id;
            }
            
        }

        $this->validate($request,[
            'title' => 'required',
            'thumbnail' => 'required'
        ]);

        $title = $request['title'];
        $thumbnail = $request['thumbnail'];
        $start_date = $request['start_date'];
        $start_time = $request['start_time'];

        if(!isset($request['is_schedule'])) {
            $request['is_schedule'] = 'no';
        }

            $live = new Live;
            $live->title = $title;
            $live->unique_id = $unique_id;
            $live->is_schedule = $request['is_schedule'];
            $live->type = $request['type'];
            $live->start_date = $start_date;
            $live->start_time = $start_time;
            $live->thumbnail = $thumbnail;
            $live->streaming_id = $request['streaming_id'];
            $live->save();

            $live_id = $live->id;

        return response()->json(['live_id' => $live_id,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function pin_message(Request $request,$live_id)
    {
        $this->validate($request,[
            'message' => 'required'
        ]);

        $message = $request['message'];

        $live = Live::find($live_id);

        $live->pin_message = $message;

        $live->save();

        return response()->json(['pin' => $message,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function quickAdd(Request $request,$live_id)
    {
        $this->validate($request,[
            'product_title' => 'required|string|max:255',
            'selling_price' => 'required|numeric|between:0,9999999999.99'
        ]);

        $product_title = $request['product_title'];
        $selling_price = $request['selling_price'];

        $live = Live::find($live_id);
        $live->product_title = $product_title;
        $live->selling_price = $selling_price;
        $live->save();

        return response()->json(['status' => 'success',
                                 'success' => true], 200);
    }

    public function changeItem(Request $request,$live_id)
    {
        $this->validate($request,[
            'product_title' => 'required|string|max:255',
            'selling_price' => 'required|numeric|between:0,9999999999.99'
        ]);

        $product_title = $request['product_title'];
        $selling_price = $request['selling_price'];

        $live = Live::find($live_id);
        $live->product_title = $product_title;
        $live->selling_price = $selling_price;
        $live->save();

        return response()->json(['status' => 'success',
                                 'success' => true], 200);
    }

    public function endLiveStream($live_id)
    {
        $end_stream = Live::find($live_id);

        $end_stream->end_date = Carbon::now();
        $end_stream->end_time = Carbon::now();

        $end_stream->save();

        return response()->json(['message' => 'stream ended',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function startSelling(Request $request,$product_id)
    {
        $live_id = $request['live_id'];

        $product = SellerProduct::find($product_id);

        $bid = BidType::where('id',$product->buy_mode_id)->first();

        $bid_type = $bid->bid_type;
        
        if(!isset($request['deposit_rate'])){
            $request['deposit_rate'] = 0;
        }

        if(!isset($request['cutoff_price'])){
            $request['cutoff_price'] = 0;
        }

        if(!isset($request['price_tick'])){
            $request['price_tick'] = 0;
        }

        if(!isset($request['tick_time'])){
            $request['tick_time'] = 0;
        }

        if(!isset($request['run_time'])){
            $request['run_time'] = 0;
        }

        if(!isset($request['run_type'])){
            $request['run_type'] = '';
        }

        if(!isset($request['bid_letters'])){
            $request['bid_letters'] = '';
        }

        if(!isset($request['timer_start'])){
            $request['timer_start'] = 0;
        }

        if(!isset($request['timer_end'])){
            $request['timer_end'] = 0;
        }

        if(!isset($request['old_price'])){
            $request['old_price'] = 0;
        }

        $product_session = new ProductSession;
        $product_session->product_id = $product_id;
        $product_session->stream_id = $live_id;
        $product_session->bid_type = $bid_type;
        $product_session->deposit_rate =  $request['deposit_rate'];
        $product_session->cutoff_price = $request['cutoff_price'];
        $product_session->price_tick = $request['price_tick'];
        $product_session->tick_time = $request['tick_time'];
        $product_session->run_time = $request['run_time'];
        $product_session->run_type = $request['run_type'];
        $product_session->bid_letters = $request['bid_letters'];
        $product_session->timer_start = $request['timer_start'];
        $product_session->timer_end = $request['timer_end'];
        $product_session->old_price = $request['old_price'];
        $product_session->quantity = $product->quantity;
        $product_session->status = 1;
        $product_session->save();

        $product_session_id = $product_session->id;


        return response()->json(['product_session_id' => $product_session_id,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function endSelling($product_session_id)
    {
        $end_selling = ProductSession::find($product_session_id);
        $end_selling->status = 3;
        $end_selling->save();

        return response()->json(['message' => 'bid ended',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function checkStock($product_session_id)
    {
        $e_commerce = ProductSession::find($product_session_id);

        $quantity = $e_commerce->quantity;

        if($quantity == 0)
        {
            $end_selling = ProductSession::find($product_session_id);
            $end_selling->status = 3;
            $end_selling->save();

            return response()->json(['message' => 'out of stock bid ended',
                                     'status' => 'success',
                                     'success' => true], 200);
        }else{
            return response()->json(['stock' => $quantity], 200);
        }
    }

    public function getBidderList($product_id)
    {
        $bidder = GetBidder::where('product_id',$product_id)
                            ->where('bid_type',1)
                            ->orderBy('product_id','DESC')
                            ->get();

        return response()->json(['bidders' => $bidder,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function getPurchaseList($product_id)
    {
        $purchase = GetBidder::where('product_id',$product_id)
                                ->where('bid_type',3)
                                ->orderBy('product_id','DESC')
                                ->get();

        return response()->json(['purchase' => $purchase,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function getAuctionLowBidder($product_id)
    {
        $bidders = GetBidder::where('product_id',$product_id)
                                ->where('bid_type',2)
                                ->orderBy('product_id','DESC')
                                ->get();

        return response()->json(['bidders' => $bidders,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function soldAHProduct(Request $request,$product_session_id)
    {
        $product_session = ProductSession::find($product_session_id);
        $product_session->status = 4;
        $product_session->save();

        $live = Live::find($product_session->stream_id);

        $stream_name = $live->title;

        $customer_id = $product_session->last_bidder;

        $quantity = $product_session->quantity;

        $cart_order = new OrderCartList;
        $cart_order->product_id = $product_session->product_id;
        $cart_order->price = $product_session->max_bid;
        $cart_order->discount_price = 0;
        $cart_order->store_name = '';
        $cart_order->stream_name = $stream_name;
        $cart_order->type = $product_session->bid_type;
        $cart_order->quantity = $quantity;
        $cart_order->customer_id = $customer_id;
        $cart_order->save();

        $order_status_id = 20;
        $invoice_prefix = 'INV-'.date('Y').'-00';
        $store_id = 0;
        $store_name = "";
        $store_url = getenv('STORE_URL');
        $customer_group_id = 1;
        $shipping_method = '';
        $currency_value = 1;
        $price = $product_session->max_bid;
        $total = $product_session->max_bid;
        $shipping = 0;
        $quantity = 0;
        
        if(!isset($request['payment_country_id'])) {
            $request['payment_country_id'] = 0;
        }

        if(!isset($request['payment_zone_id'])) {
            $request['payment_zone_id'] = 0;
        }

        if(!isset($request['shipping_lat'])) {
            $request['shipping_lat'] = 0;
        }

        if(!isset($request['shipping_lon'])) {
            $request['shipping_lon'] = 0;
        }

        if(!isset($request['affiliate_id'])) {
            $request['affiliate_id'] = 0;
        }

        if(!isset($request['commission'])) {
            $request['commission'] = 0.0000;
        }

        if(!isset($request['marketing_id'])) {
            $request['marketing_id'] = 0;
        }

        $customer = Customer::where('user_id',$customer_id);

        $customerAddress = DB::table('oc_address')
                            ->select('oc_address.*')
                            ->leftJoin('oc_customer', 'oc_customer.address_id','oc_address.address_id')
                            ->where('oc_address.customer_id',$customer_id)
                            ->orderBy('oc_address.address_id','desc')
                            ->first();

        $order = new Order();
        $order->invoice_no = '';
        $order->invoice_prefix = $invoice_prefix;
        $order->store_id = $store_id;
        $order->store_name = $store_name;
        $order->store_url = $store_url;
        $order->customer_id = $customer->customer_id;
        $order->customer_group_id = $customer_group_id;
        $order->firstname = $customer->firstname;
        $order->lastname = $customer->lastname;
        $order->email = $customer->email;
        $order->telephone = $customer->telephone;
        $order->fax = $customer->fax;
        $order->custom_field = $customer->custom_field;
        $order->payment_firstname = $customer->firstname;
        $order->payment_lastname = $customer->lastname;
        $order->payment_company = "";
        $order->payment_address_1 = "";
        $order->payment_address_2 = "";
        $order->payment_city = "";
        $order->payment_postcode = "";
        $order->payment_country = "";
        $order->payment_country_id = $request->get('payment_country_id');
        $order->payment_zone = "";
        $order->payment_zone_id = $request->get('payment_zone_id');
        $order->payment_address_format = "";
        $order->payment_custom_field = "";
        $order->payment_method = "";
        $order->payment_code = "";
        $order->shipping_firstname = $customer->firstname;
        $order->shipping_lastname = $customer->lastname;
        $order->shipping_company = $customerAddress->company;
        $order->shipping_address_1 = $customerAddress->address_1;
        $order->shipping_address_2 = $customerAddress->address_2;
        $order->shipping_city = $customerAddress->city;
        $order->shipping_country = "";
        $order->shipping_country_id = $customerAddress->country_id;
        $order->shipping_zone = "";
        $order->shipping_zone_id = $customerAddress->zone_id;
        $order->shipping_address_format = "";
        $order->shipping_custom_field = "";
        $order->shipping_method = $shipping_method;
        $order->shipping_code = "";
        $order->shipping_postcode = "";
        $order->courier_id = "";
        $order->shipping_lat = $request['shipping_lat'];
        $order->shipping_lon = $request['shipping_lon'];
        $order->comment = "";
        $order->type = "";
        $order->total = $total;
        $order->order_status_id = $order_status_id;
        $order->affiliate_id = $request['affiliate_id'];
        $order->commission = $request['commission'];
        $order->marketing_id = $request['marketing_id'];
        $order->tracking = "";
        $order->postage_company = "";
        $order->language_id = 1;
        $order->currency_id = 4;
        $order->currency_code = 'MYR';
        $order->currency_value = 1;
        $order->ip = $request->ip();
        $order->forwarded_ip = "";
        $order->user_agent = $request['HTTP_USER_AGENT'];
        $order->accept_language = "";
        $order->unique_id = $customer->user_id;
        $order->date_added = Carbon::now()->toDateTimeString();
        $order->date_modified = Carbon::now()->toDateTimeString();
        $order->save();

        $order_id = $order->order_id;

        $order_product = new OrderProduct();
        $order_product->order_id = $order_id;
        $order_product->product_id = 0;
        $order_product->name = "";
        $order_product->model = "";
        $order_product->shipping = $shipping;
        $order_product->quantity = $quantity;
        $order_product->price = $total;
        $order_product->bid_amount = $total;
        $order_product->total = $total;
        $order_product->tax = 0;
        $order_product->reward = 0;
        $order_product->save();

        $purpletree_order = new PurpleTreeOrder();
        $purpletree_order->seller_id = 0;
        $purpletree_order->product_id = 0;
        $purpletree_order->order_id = $order_id;
        $purpletree_order->quantity = $quantity;
        $purpletree_order->unit_price = 0;
        $purpletree_order->shipping = $shipping;
        $purpletree_order->total_price = $total;
        $purpletree_order->order_status_id = $order_status_id;
        $purpletree_order->created_at = Carbon::now()->toDateTimeString();
        $purpletree_order->updated_at = Carbon::now()->toDateTimeString();
        $purpletree_order->save();

        return response()->json(['message' => 'product sold',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function get_e_commerce_Product(Request $request,$product_id)
    {
        $product = DB::table('oc_product')
                        ->select('oc_product.*')
                        ->where('oc_product.product_id',$product_id)
                        ->where('oc_product.buy_mode','marketplace')
                        ->where('oc_product.sell_mode',$request['sell_mode'])
                        ->first();

        $product_sold = DB::table('oc_product')
                        ->select('oc_product.*','oc_order_product.*')
                        ->join('oc_order_product','oc_order_product.product_id','=','oc_product.product_id')
                        ->where('oc_product.product_id',$product_id)
                        ->where('oc_product.buy_mode','marketplace')
                        ->where('oc_product.sell_mode',$request['sell_mode'])
                        ->get()
                        ->count();

        $product_variation = DB::table('oc_product')
                            ->select('oc_product.*','oc_product_variations_data.*')
                            ->join('oc_product_variations_data','oc_product_variations_data.variation_id','=','oc_product.product_id')
                            ->where('oc_product.product_id',$product_id)
                            ->where('oc_product.buy_mode','marketplace')
                            ->where('oc_product.sell_mode',$request['sell_mode'])
                            ->get()
                            ->count();

        $product_name = $product->product_title;
        $product_price = $product->price;

        $data['product'] = array([ 
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_sold' => $product_sold,
            'product_variation' => $product_variation,
          ]);

        return response()->json(['product_detail' => $data,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function getALProduct(Request $request, $product_id)
    {
        $product = DB::table('oc_product')
                        ->select('oc_product.*','oc_product_shipping_options.*','oc_product_session.*')
                        ->join('oc_product_shipping_options','oc_product_shipping_options.product_id','=','oc_product.product_id')
                        ->join('oc_product_session','oc_product_session.product_id','=','oc_product.product_id')
                        ->where('oc_product.product_id',$product_id)
                        ->where('oc_product.buy_mode','auction_low')
                        ->where('oc_product.sell_mode',$request['sell_mode'])
                        ->first();
        if($product === null)
        {
            $product_name = null;
            $product_price = null;
            $game_duration = null;
            $product_availability = null;
            $product_shipping_fees = null;
        }
        else
        {
            $product_name = $product->title;
            $product_price = $product->starting_price;
            $game_duration = $product->game_duration;
            $product_availability = $product->quantity;
            $product_shipping_fees = $product->shipping_fees;
        }
        
        

        $data['product'] = array([ 
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_availability' => $product_availability,
            'duration' => $game_duration
          ]);

        return response()->json(['product_detail' => $data,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function e_commercePurchase(Request $request,$product_id)
    {
        $sold_to = DB::table('oc_product')
                        ->select('oc_product.*','oc_order_product.*','oc_order.*')
                        ->join('oc_order_product','oc_order_product.product_id','=','oc_product.product_id')
                        ->join('oc_order','oc_order.order_id','=','oc_order_product.order_id')
                        ->where('oc_product.product_id',$product_id)
                        ->where('oc_product.buy_mode','marketplace')
                        ->where('oc_product.sell_mode','livestream')
                        ->get();

        foreach ($sold_to as $sold)
        {
            $data['sold'] = array([ 
                'buyer_name' => $sold['lastname'].' '.$sold['firstname'],
                'purchase' => $sold['quantity']
              ]);
        }

        return response()->json(['sold' => $data,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function auctionLowPurchase($product_id)
    {
        $sold_to = DB::table('oc_product')
                        ->select('oc_product.*','oc_order_product.*','oc_order.*')
                        ->join('oc_order_product','oc_order_product.product_id','=','oc_product.product_id')
                        ->join('oc_order','oc_order.order_id','=','oc_order_product.order_id')
                        ->where('oc_product.product_id',$product_id)
                        ->where('oc_product.buy_mode','auction_low')
                        ->where('oc_product.sell_mode','livestore')
                        ->get();

        foreach ($sold_to as $sold)
        {
            $data['sold'] = array([ 
                'buyer_name' => $sold['lastname'].' '.$sold['firstname'],
                'purchase' => $sold['quantity']
              ]);
        }

        return response()->json(['sold' => $data,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function getStreamParticipants($product_session_id)
    {
        $participants = DB::table('oc_stream_product_participants')
                            ->select('oc_stream_product_participants.*','oc_customer.*')
                            ->join('oc_customer','oc_customer.user_id','=','oc_stream_product_participants.user_id')
                            ->where('oc_stream_product_participants.session_id',$product_session_id)
                            ->get();

        //oc_stream_product_participants.session_id is oc_stream_product_bids Foreign Key

        foreach ($participants as $participant)
        {
            $data['participant'] = array([ 
                'name' => $participant['lastname'].' '.$participant['firstname']
              ]);
        }

        return response()->json(['participant' => $data,
                                 'product_session_id' => $product_session_id]);
    }

    public function luckyDrawRoll($product_session_id)
    {
        $winner = Participants::where('session_id',$product_session_id)->get()->random(1);

        $winner_id = $winner[0]['user_id'];

        $winner_name = Customer::where('customer_id',$winner_id)->first();

        return response()->json(['winner' => $winner_name->username]);
    }
}
