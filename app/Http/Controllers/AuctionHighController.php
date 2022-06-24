<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductSession;
use App\Models\LiveStores;
use App\Models\SellerProduct;
use App\Models\BidType;
use App\Models\Customer;
use App\Models\UserBidHistory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderCartList;
use App\Models\PurpleTreeOrder;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Events\UpdatePriceEvent;

class AuctionHighController extends Controller
{
    public function getStoreProduct(Request $request)
    {
        $product = DB::table('oc_product')
                        ->select('oc_product.*','oc_product_variation.*','oc_product_specifications.*')
                        ->join('oc_product_variation','oc_product_variation.product_id','=','oc_product.product_id')
                        ->join('oc_product_specifications','oc_product_specifications.product_id','=','oc_product.product_id')
                        ->where('buy_mode',$request['buy_mode'])
                        ->where('sell_mode','livestore')
                        ->get();

        return response()->json(['product' => $product,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function getBidderList(Request $request, $product_id)
    {
        $bidders = [
            [
                "name" => "Kent",
                "profile_url" => "https://media.istockphoto.com/photos/husky-dog-picture-id931911044",
                "amount" => "1000"
            ],
            [
                "name" => "Ziyad",
                "profile_url" => "https://thumbs.dreamstime.com/z/little-dog-car-toy-3683639.jpg",
                "amount" => "900"
            ],
            [
                "name" => "Aziz",
                "profile_url" => "https://www.akc.org/wp-content/uploads/2017/01/Australian-Shepherds-running-together.jpeg",
                "amount" => "800"
            ],
            ];

            return response()->json(['bidders'=> $bidders,
                                     'status' => 'success',
                                     'success' => true], 201);


    }

    public function auctionHighBidderList(Request $request)
    {
        $data = array();
        $bid_session = $request['session_id'];

        $bidders = DB::table('oc_stream_product_participants')
                        ->select('oc_stream_product_participants.*','oc_customer.*')
                        ->join('oc_customer','oc_customer.customer_id','=','oc_stream_product_participants.user_id')
                        ->where('oc_stream_product_participants.session_id',$bid_session)
                        ->get();

        $bidders->transform(function($i) {
                return (array)$i;
        });

        $array = $bidders->toArray();

        foreach($bidders as $bidder)
        {
            $data['order'][]=array(
                'id' => $bidder['customer_id'],
                'name' => $bidder['firstname'].' '. $bidder['lastname'],
                'profile_url' => $bidder['profile_url'],
                'amount' => $bidder['amount']
            );
        }

        return response()->json($data);
    }

    public function getParticipantCount(Request $request,$bid_session)
    {
        $participant = DB::table('oc_stream_product_participants')
                        ->select('*')
                        ->where('session_id',$bid_session)
                        ->get()
                        ->count();

        return response()->json(['participants' => $participant,
                                 'status' => 'success',
                                 'success' => true], 201);
    }

    public function getBiddersCount(Request $request,$bid_session)
    {
        $bids = DB::table('oc_live_bid_history')
                        ->select('customer_id')
                        ->where('bid_session_id',$bid_session)
                        ->get()
                        ->count();

        return response()->json(['participants' => $bids,
                                 'status' => 'success',
                                 'success' => true], 201);
    }

    public function bidWon(Request $request)
    {
        $bid_session = $request['bid_session'];

        $winner = UserBidHistory::where('bid_session_id',$bid_session)
                                    ->orderBy('amount','DESC')
                                    ->first();
        if($winner === null)
        {
            $winner_id = 0;
        }
        else
        {
            $winner_id = $winner->customer_id;
        }
        
                                    
        $participant = LiveStores::where('customer_id',$winner_id)
                                    ->where('session_id',$bid_session)
                                    ->first();

        if($participant === null)
        {
             $participant->winner = 0;
        }
        else
        {
             $participant->winner = $winner_id;
        }
       
        $participant->save();

        $customer = Customer::where('customer_id',$winner_id)->first();

        $product_info = SellerProduct::find($participant->product_id);

        $bid_type = BidType::find($product_info->buying_mode_id);

        $bid_name = $bid_type->bid_type;

        $customerAddress = DB::table('oc_address')
                            ->select('oc_address.*')
                            ->leftJoin('oc_customer', 'oc_customer.address_id','oc_address.address_id')
                            ->where('oc_address.customer_id',$winner_id)
                            ->orderBy('oc_address.address_id','desc')
                            ->first();

        $order_status_id = 20;
        $invoice_prefix = 'INV-'.date('Y').'-00';
        $store_id = 0;
        $store_name = "";
        $store_url = getenv('STORE_URL');
        $customer_group_id = 1;
        $shipping_method = '';
        $currency_value = 1;
        $price = $product_info->final_price;
        $total = $product_info->final_price;
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

        $order = new Order;
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
        $order->shipping_lat = $request->get('shipping_lat');
        $order->shipping_lon = $request->get('shipping_lon');
        $order->comment = "";
        $order->type = "";
        $order->total = $total;
        $order->order_status_id = $order_status_id;
        $order->affiliate_id = $request->get('affiliate_id');
        $order->commission = $request->get('commission');
        $order->marketing_id = $request->get('marketing_id');
        $order->tracking = "";
        $order->postage_company = "";
        $order->language_id = 1;
        $order->currency_id = 4;
        $order->currency_code = 'MYR';
        $order->currency_value = 1;
        $order->ip = $request->ip();
        $order->forwarded_ip = "";
        $order->user_agent = $request->server('HTTP_USER_AGENT');
        $order->accept_language = "";
        $order->unique_id = $customer->user_id;
        $order->date_added = Carbon::now()->toDateTimeString();
        $order->date_modified = Carbon::now()->toDateTimeString();
        $order->save();

        $order_id = $order->order_id;

        $order_product = new OrderProduct;
        $order_product->order_id = $order_id;
        $order_product->product_id = $participant->product_id;
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

        $purpletree_order = new PurpleTreeOrder;
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

        $cart_order = new OrderCartList;
        $cart_order->product_id = $product_info->id;
        $cart_order->price = $product_info->final_price;
        $cart_order->discount_price = 0;
        $cart_order->order_id = $order_id;
        $cart_order->order_status = 20;
        $cart_order->store_name = '';
        $cart_order->stream_name = '';
        $cart_order->type = $bid_name;
        $cart_order->quantity = 1;
        $cart_order->customer_id = $customer->customer_id;
        $cart_order->save();

        return response()->json(['status' => 'success',
                                 'success' => true], 200);
    }
}
