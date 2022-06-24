<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PurpleTreeOrder;
use App\Models\PurpleTreeStore;
use App\Models\Address;
use App\Models\Cart;
use App\Models\OrderCartList;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Discount;
use App\Models\Wallet;
use App\Models\MemberWalletTransaction;
use App\Models\BidTransaction;
use App\Models\CancelOrder;
use App\Models\CancelOrderReasons;
use App\Models\ProductImage;
use App\Models\Refund;
use App\Models\RefundStatus;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Support\Facades\Session;

class BuyProductController extends Controller
{


    public function getDiscountPrice(Request $request, $product_id)
    {
        $tomorrow = new DateTime('tomorrow');
        $user = auth()->user();
        $user_unique_id = $user->user_id;

        $promotion_code = $request->input('promotion_code');

        $promotion = Promotion::where('promotion_code',$promotion_code)
                                ->where('status', 1)
                                ->first();

        if(!$promotion)
        {
            return response()->json(['message'=> 'Invalid promotion code',
                                     'status' => 'failed'], 404);
        }else{

            if($promotion->discount_type == 'discount_amount'){
            // $discount_id = $promotion->discount_id;

            // $discount = Discount::where('id',$discount_id)->first();

            // $discounted = $discount->discount_amount;

            $discounted = $promotion->discount_amount;

            $product = Product::where('product_id', $product_id)->first();

            $discount_price = $product->price - $discounted;

            //Send Notification to Member
             DB::table('oc_notifications')->insert([
                 'customer_id' => 0,
                 'from_customer_id' => 0,
                 'type' => 'promotions',
                 'notification_title' => "Use this voucher code: " . $promotion_code ,
                 'notification_message' => "Use this voucher code: " . $promotion_code ,
                 'notification_action' => 'promotion for member',
                 'notification_interaction' => "Use this voucher code: " . $promotion_code ,
                 'notification_is_read' => 0,
                 'notification_datetime' => Carbon::now(),
                 'notification_expire_datetime' => $tomorrow,
                 'unique_id' => $user_unique_id,
                 'from_unique_id' => 0
             ]);

              return response()->json(['result' => $discount_price,
                                       'status' => 'success'], 200);

            }
            elseif($promotion->discount_type = 'percentage_off'){

                $discounted = $promotion->discount_amount;

                $product = Product::where('product_id', $product_id)->first();

                $percentage_price = $product->price * $discounted / 100;

                $total = $product->price - $percentage_price;

                //Send Notification to Member
                DB::table('oc_notifications')->insert([
                    'customer_id' => 0,
                    'from_customer_id' => 0,
                    'type' => 'promotions',
                    'notification_title' => "Use this voucher code: " . $promotion_code ,
                    'notification_message' => "Use this voucher code: " . $promotion_code ,
                    'notification_action' => 'promotion for member',
                    'notification_interaction' => "Use this voucher code: " . $promotion_code ,
                    'notification_is_read' => 0,
                    'notification_datetime' => Carbon::now(),
                    'notification_expire_datetime' => $tomorrow,
                    'unique_id' => $user_unique_id,
                    'from_unique_id' => 0
                ]);
                
                return response()->json(['result' => $total,
                                       'status' => 'success'], 200);
            }
            
        }

    }

    public function usePromotion(Request $request){

        $promotion_id = $request['promotion_id'];

        
    }

    public function removePromotion($id)
    {
        $product = Product::find($id);

        $price = $product->price;

        return response()->json(['result' => $price,
                                 'status' => 'success'], 200);

    }

    public function cancelOrder(Request $request,$order_list_id)
    {
        $user = auth()->user();
        $customer = $user;

        $this->validate($request,[
            'cancel_reason' => 'required',
        ]);

        $cancel_reason_id = $request['cancel_reason'];

        $cancel_reasons = CancelOrderReasons::find($cancel_reason_id);
        $reason = $cancel_reasons->cancel_reason;
        $variation = $request->input('variation');

        $variation_size = $request->input('variation_size');
        $cancel_orders = DB::table('oc_order_cart_list')
                            ->select('oc_order_cart_list.*','oc_product_description.name','oc_product.image')
                            ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                            ->join('oc_product_description','oc_product_description.product_id','=','oc_product.product_id')
                            ->where('oc_order_cart_list.id',$order_list_id)
                            ->first();

        $order_cancel = new CancelOrder;
        $order_cancel->order_id = $cancel_orders->order_id;
        $order_cancel->order_cart_list_id = $cancel_orders->id;
        $order_cancel->cancel_by = $customer->user_id;
        $order_cancel->cancel_reason = $reason;
        $order_cancel->save();

        $order_cancel_id = $order_cancel->id;
        $order_cancel_time = $order_cancel->created_at;

        $order_cart_list = OrderCartList::where('id',$order_list_id)->update(['order_status' => 0,
                                                                               'cancellation_time' => $order_cancel_time,
                                                                               'updated_at' => DB::raw('updated_at')]);


        // $cancelOrder = Order::find($order_id);
        // $cancelOrder->order_status_id = 0;
        // $cancelOrder->save();

        // $purpletree_order_cancel = PurpleTreeOrder::find($order_id);
        // $purpletree_order_cancel->order_status_id = 0;
        // $purpletree_order_cancel->save();
    
        $order_status_id = $cancel_orders->order_status;

        $order_time = $cancel_orders->created_at;
        $payment_time = $cancel_orders->updated_at;
        $cancelled_time = $cancel_orders->cancellation_time;

        $order_status = OrderStatus::where('order_status_id',$order_status_id)->first();

        $orderStatus = $order_status->name;

        $address = Address::where('customer_id',$customer->customer_id)->first();

        $home_address = $address->address_1.' '.$address->address_2;
        $name = $address->firstname.' '.$address->lastname;

        $phone_number = $customer->telephone_countrycode.$customer->telephone;

        $data['order_details']= array(
            'order_id' => $cancel_orders->order_id,
            'order_time' => $cancel_orders->created_at,
            'payment_time' => $cancel_orders->updated_at,
            'cancellation_time' => $cancel_orders->cancellation_time,
            'stream_name' => $cancel_orders->stream_name,
            'game_mode' => $cancel_orders->type,
            'order_status' => $orderStatus,
          );

        $data['delivery_address']= array(
            'name' => $name,
            'address'=> $home_address,
            'phone_number' => $phone_number
          );

            $data['order'][]=array(
                'store_name' => $cancel_orders->store_name,
                'product_image' => $cancel_orders->image,
                'product_name' => $cancel_orders->name,
                'quantity' => $cancel_orders->quantity,
                'price' => $cancel_orders->price,
                'discount_price' => $cancel_orders->discount_price,
                'variation' => 'Blue,L',
                'shipping' => $cancel_orders->shipping,
                'type' => $cancel_orders->type,
                'total' => $cancel_orders->price - $cancel_orders->discount_price,
                'order_cart_list_id' => $cancel_orders->id
            );

            $refund = new Refund;
            $refund->order_cancel_id = $order_cancel_id;
            $refund->return_id = 0;
            $refund->refund_amount = $cancel_orders->price;
            $refund->status = 1;
            $refund->save();

        return response()->json(['cancel_order' => $data,
                                 'status' => 'success'], 200);
    }

    public function cancelDetails($order_cancel_id,$order_cart_list_id)
    {

        $data = array();
        $order_cancel = CancelOrder::where('id',$order_cancel_id)
                                    ->where('order_cart_list_id',$order_cart_list_id)
                                    ->first();
        if($order_cancel === null)
        {
            $order_cart_list_id = null;

            $cancel_order_id = null;
        }
        else
        {
            $order_cart_list_id = $order_cancel->order_cart_list_id;

            $cancel_order_id = $order_cancel->id;
        }

        $product = OrderCartList::find($order_cart_list_id);

        $refund = Refund::where('order_cancel_id',$cancel_order_id)->first();
        if($refund === null)
        {
            $refund_status = null;
        }
        else
        {
            $refund_status = $refund->status;
        }

        $status = RefundStatus::find($refund_status);

        if($status === null)
        {

        }

        else{
            $data['product_details']=array(
                'order_total' => $product->price,
                'shipping_fee' => $product->shipping,
                'refund_amount' => $product->price
            );

            $data['cancellation_details']=array(
                'order_time' => $product->created_at,
                'payment_time' => $product->updated_at,
                'requested_at' => $product->cancellation_time,
                'requested_by' => $order_cancel->cancel_by,
                'requeted_id' => $order_cancel->id,
            );

            $data['reasons'] = array(
                'reasons' => $order_cancel->cancel_reason
            );

            $data['refund_status'] = array(
                'status' => $status->refund_status,
                'paid_at' =>$refund->paid_at
            );
        }
        return response()->json(['result'=> $data,
                                 'status' => 'success'], 200);
    }

    public function orderPaid(Request $request,$order_id)
    {

        $user = auth()->user();
        $customer = $user;

        $order_status_id = 2;
        
        $orders = DB::table('oc_order_cart_list')
                    ->select('oc_order_cart_list.*','oc_product.*','oc_product_variations.*')
                    ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                    ->join('oc_product_variations','oc_product_variations.product_id','=','oc_product.product_id')
                    ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                    ->where('oc_order_cart_list.order_status',$order_status_id)
                    ->where('oc_order_cart_list.order_id',$order_id)
                    ->get();

        $orders->transform(function($i) {
                return (array)$i;
        });

        $array = $orders->toArray();

        if (!$array)
        {
                return response()->json(['message' => 'Orders Not Found',
                                        'status'=>'failed'
                                        ], 404);    
        }
    
        $order_status_id = $orders[0]['order_status'];

        $order_time = $orders[0]['created_at'];
        $payment_time = $orders[0]['updated_at'];

        $order_status = OrderStatus::where('order_status_id',$order_status_id)->first();

        $orderStatus = $order_status->name;

        $address = Address::where('customer_id',$customer->customer_id)->first();

        $home_address = $address->address_1.' '.$address->address_2;
        $name = $address->firstname.' '.$address->lastname;

        $phone_number = $customer->telephone_countrycode.$customer->telephone;

        $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'order_status' => $orderStatus,
          );

        $data['delivery_address']= array(
            'name' => $name,
            'address'=> $home_address,
            'phone_number' => $phone_number
          );

          
          foreach ($orders as $order)
        {
            $data['order'][]=array(
                'store_name' => $order['store_name'],
                'product_image' => $order['image'],
                'product_name' => $order['title'],
                'quantity' => $order['quantity'],
                'price' => $order['price'],
                'discount_price' => $order['discount_price'],
                'variation' => $order['variation_title'].','.$order['variation_value'],
                'shipping' => $order['shipping'],
                'type' => $order['buy_mode']
            );
        }

          return response()->json(['result'=>$data,
                                   'status' => 'success'], 200);
    }

    public function orderDetails(Request $request,$order_cart_list_id)
    {

        $user = auth()->user();
        $customer = $user;

        $orders = DB::table('oc_order_cart_list')
                    ->select('oc_order_cart_list.*','oc_product.*','oc_product_variations.*')
                    ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                    ->join('oc_product_variations','oc_product_variations.product_id','=','oc_product.product_id')
                    ->where('oc_order_cart_list.id',$order_cart_list_id)
                    ->first();

        // dd($orders);

        if($orders === null)
        {
            $order_status_id = null;
            $order_id = null;
            $order_time = null;
            $payment_time = null;
            $stream_name = null;
            $game_mode = null;
        }
        else{
            $order_status_id = $orders->order_status;
            $order_id = $orders->order_id;
            $order_time = $orders->created_at;
            $payment_time = $orders->updated_at;
            $stream_name = $orders->stream_name;
            $game_mode = $orders->buy_mode;
        }

        

        

        $order_status = OrderStatus::where('order_status_id',$order_status_id)->first();

        if($order_status === null)
        {
             $orderStatus = null;
        }
        else
        {
             $orderStatus = $order_status->name;
        }
       

        $address = Address::where('customer_id',$customer->customer_id)->first();

        if($address === null)
        {
              $home_address = null;
            $name = null;
        }
        else
        {
             $home_address = $address->address_1.' '.$address->address_2;
            $name = $address->firstname.' '.$address->lastname;
        }

        

        $phone_number = $customer->telephone_countrycode.$customer->telephone;

        $order_status = $orderStatus;

        switch($order_status){
          case 'Unpaid':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'order_status' => $orderStatus
          ); 
          break;

          case 'Paid':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'order_status' => $orderStatus
          ); 
          break;

          case 'Cancelled':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'cancellation_time' => $orders->cancellation_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'order_status' => $orderStatus
          ); 
          break;

          case 'To Ship':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'ship_time' => '',
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'tracking_number' => '',
            'order_status' => $orderStatus
          ); 
          break;

          case 'To Receive':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'ship_time' => '',
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'tracking_number' => '',
            'order_status' => $orderStatus
          ); 
          break;

          case 'Delivered':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'ship_time' => '',
            'completed_time' => '',
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'tracking_number' => '',
            'order_status' => $orderStatus
          ); 
          break;

          case 'Forfeited':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'order_status' => $orderStatus
          ); 
          break;

          default:
            return response()->json(['message' => 'something went wrong',
                                     'status' => 'failed'], 404);

        }

        $data['delivery_address']= array(
            'name' => $name,
            'address'=> $home_address,
            'phone_number' => $phone_number
          );

        $data['order']=array(
                'store_name' => $orders->store_name,
                'product_image' => $orders->image,
                'product_name' => $orders->title,
                'quantity' => $orders->quantity,
                'price' => $orders->price,
                'discount_price' => $orders->discount_price,
                'variation' => $orders->variation_title.','.$orders->variation_value,
                'shipping' => $orders->shipping,
                'type' => $orders->buy_mode
            );

          return response()->json(['result'=>$data,
                                   'status' => 'success'], 200);
    }

    public function updateCartPrice(Request $request,$order_cart_id)
    {
        $quantity = $request['quantity'];

        $variation_1 = $request['variation_1'];

        $variation_2 = $request['variation_2'];

        $variations = $variation_1 . $variation_2;

        $order_cart_list = OrderCartList::find($order_cart_id);

        if($order_cart_list === null)
        {
            $price = null;
        }
        else
        {
            $price = $order_cart_list->price;
        }
        
        $total_price = $price * $quantity;

        if($variations !== null){
        $order_cart_list->update([
                'total_price' => $total_price,
                'quantity'    => $quantity,
                'variation_1' => $variation_1,
                'variation_2' => $variation_2
            ]);
        }else{
        $order_cart_list->update([
                'total_price' => $total_price,
                'quantity'    => $quantity,
                'variation_1' => $order_cart_list->variation_1,
                'variation_2' => $order_cart_list->variation_2
            ]);        
        }
        

        return response()->json(['total price' => $total_price,
                                 'status' => 'success'], 200);
    }

    public function addToOrderCart(Request $request,$product_id)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $this->validate($request,[
            'quantity' => 'required'
        ]);

        $quantity = $request['quantity'];

        $product = DB::table('oc_product')->where('product_id', $product_id)->first();

        if($product === null)
        {
            $seller = array();
        }
        else
        {
             $store_id = $product->store_id;

        $seller = DB::table('oc_purpletree_vendor_stores')
                        ->select('*')
                        ->where('seller_unique_id',$store_id)
                        ->first();

        $variation_1 = $request['variation_1'];

        $variation_2 = $request['variation_2'];

        $variation = $variation_1 . $variation_2;

        if($variation != null)
        {
            $cart_order = new OrderCartList;
            $cart_order->product_id = $product->product_id;
            $cart_order->price = $product->price;
            $cart_order->total_price = $product->price * $quantity;
            $cart_order->store_name = $seller->store_name;
            $cart_order->type = 'marketplace';
            $cart_order->quantity = $quantity;
            $cart_order->customer_id = $user_id;
            $cart_order->order_status = 7;
            $cart_order->variation_1 = $variation_1;
            $cart_order->variation_2 = $variation_2;
        } else{
            $cart_order = new OrderCartList;
            $cart_order->product_id = $product->product_id;
            $cart_order->price = $product->price;
            $cart_order->total_price = $product->price * $quantity;            
            $cart_order->store_name = $seller->store_name;
            $cart_order->type = 'marketplace';
            $cart_order->quantity = $quantity;
            $cart_order->customer_id = $user_id;
            $cart_order->order_status = 7;
        }
            $cart_order->save();
        }

       

        //Send Notification to Member
        // $tomorrow = new DateTime('tomorrow');

        // $product_image = ProductImage::where('product_id', $product['product_id'])->first();

        // $image = $product_image->image;


        // DB::table('oc_notifications')->insert([
        //     'customer_id' => 0,
        //     'from_customer_id' => 0,
        //     'type' => 'buy_mode',
        //     'notification_title' => "Pending payment" ,
        //     'notification_message' => "Pending payment" ,
        //     'notification_action' => 'buy mode member',
        //     'notification_interaction' => "Pending payment" ,
        //     'notification_is_read' => 0,
        //     'notification_datetime' => Carbon::now(),
        //     'notification_expire_datetime' => $tomorrow,
        //     'unique_id' => $user_id,
        //     'from_unique_id' => 0,
        //     'profile_image' => $image
        // ]);


          return response()->json(['status' => 'success'], 200);  
    }

    public function myCart(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->user_id;
        $data = array();

        $cart_orders = DB::table('oc_order_cart_list')
                        ->select('oc_product.*','oc_order_cart_list.*')
                        ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                        ->where('oc_order_cart_list.customer_id',$user_id)
                        ->orderBy('oc_order_cart_list.created_at','desc')
                        ->get();

        $cart_orders->transform(function($i) {
                return (array)$i;
        });

        $array = $cart_orders->toArray();

        foreach ($cart_orders as $cart_order)
        {
            $data['cart_order'][]=array(
                'store_name' => $cart_order['store_name'],
                'product_image' => $cart_order['image'],
                'product_name' => $cart_order['title'],
                'quantity' => $cart_order['quantity'],
                'price' => $cart_order['price'] * $cart_order['quantity'],
                'discount_price' => $cart_order['discount_price'],
                'variation' => $cart_order['variation_1'].','.$cart_order['variation_2'],
                'shipping' => $cart_order['shipping'],
                'type' => $cart_order['type'],
                'order_cart_id' => $cart_order['id']
            );
        }

        return response()->json(['order' => $data,
                                 'status' => 'success'],200);
    }

    public function myCartTotal(Request $request)
    {
        $cart_ids = $request['cart_ids'];

        // dd($cart_ids);

        $cart_total = DB::table('oc_order_cart_list')
                            ->select(DB::raw("SUM(oc_order_cart_list.total_price)as total"))
                            ->whereIn('oc_order_cart_list.id', $cart_ids)
                            ->get();

        return response()->json(['result' => $cart_total,
                                 'status' => 'success'], 200);
    }

    public function myCartCheckOut(Request $request)
    {
        $user = auth()->user();
        $customer = $user;

        $cart_ids = $request['cart_ids'];

        $cart_total = DB::table('oc_order_cart_list')
                            ->select(DB::raw("SUM(oc_order_cart_list.total_price) as total"))
                            ->whereIn('oc_order_cart_list.id',$cart_ids)
                            ->first();
        
        $cart_list = DB::table('oc_order_cart_list')
                            ->select('*')
                            ->whereIn('oc_order_cart_list.id', $cart_ids)
                            ->first();


        $customerAddress = DB::table('oc_address')
                            ->select('oc_address.*')
                            ->leftJoin('oc_customer', 'oc_customer.address_id','oc_address.address_id')
                            ->where('oc_address.customer_id',$customer->user_id)
                            ->orderBy('oc_address.address_id','desc')
                            ->first();

        $product = DB::table('oc_product')
                        ->select('oc_product.*','oc_order_cart_list.*','oc_product_image.*', 'oc_product_shipment.*', 'oc_product_shipment_region.*')
                        ->join('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
                        ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                        ->join('oc_product_shipment','oc_product_shipment.product_id','=','oc_product.product_id')
                        ->join('oc_product_shipment_region', 'oc_product_shipment_region.shipment_id','=','oc_product.product_id')
                        ->whereIn('oc_order_cart_list.id',$cart_ids)
                        ->first();

        if($product === null)
        {
            $seller = array();
        }
        else
        {


        $seller = DB::table('oc_purpletree_vendor_stores')
                    ->select('*')
                    ->where('seller_unique_id',$product->store_id)
                    ->first();
                }

        if(!$product){
            return response()->json(['message' => 'not found',
                                     'status' => 'failed'], 404);
        }

        $order_status_id = 7;
        $invoice_prefix = 'INV-'.date('Y').'-00';
        // $store_id = 0;
        $store_id = $seller->seller_unique_id;
        $store_name = $seller->store_name;
        $store_url = getenv('STORE_URL');
        $customer_group_id = 1;
        $shipping_method = '';
        $currency_value = 1;
        $price = $cart_total->total;
        $total = $cart_total->total;
        $shipping = 0;
        $quantity = $cart_list->quantity;
        
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
        $order->payment_country_id = $request['payment_country_id'];
        $order->payment_zone = "";
        $order->payment_zone_id = $request['payment_zone_id'];
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
        $order->shipping_country = $customerAddress->state;
        $order->shipping_country_id = $customerAddress->country_id;
        $order->shipping_zone = "";
        $order->shipping_zone_id = $customerAddress->zone_id;
        $order->shipping_address_format = "";
        $order->shipping_custom_field = "";
        $order->shipping_method = $shipping_method;
        $order->shipping_code = "";
        $order->shipping_postcode = $customerAddress->postcode;
        $order->courier_id = "";
        $order->shipping_lat = $request['shipping_lat'];
        $order->shipping_lon = $request['shipping_lon'];
        $order->comment = "";
        $order->type = $product->buy_mode;
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
        $order->user_agent = $request->server('HTTP_USER_AGENT');
        $order->accept_language = "";
        $order->unique_id = $customer->user_id;
        $order->date_added = Carbon::now()->toDateTimeString();
        $order->date_modified = Carbon::now()->toDateTimeString();
        $order->save();

        $order_id = $order->order_id;
        $shipping = $product->fee;
        $reward = $tax = 0;

        // $order_cart_lists = OrderCartList::whereIn('id',$cart_ids)
        //                                     ->update(['order_id' => $order_id,
        //                                               'order_status' => $order_status_id]);

        $cart_items = DB::table('oc_order_cart_list')
                            ->select('oc_product.*','oc_order_cart_list.*')
                            ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                            ->whereIn('oc_order_cart_list.id',$cart_ids)
                            ->get();

        $cart_items->transform(function($i) {
                return(array)$i;
        });

        $array = $cart_items->toArray();

        foreach ($cart_items as $cart_item)
        {

        $order_product = new OrderProduct;
        $order_product->order_id = $order_id;
        $order_product->product_id = $cart_item['product_id'];
        $order_product->name = $cart_item['title'];
        $order_product->model = "";
        $order_product->shipping = $shipping;
        $order_product->quantity = $cart_item['quantity'];
        $order_product->price = $cart_item['price'];
        $order_product->bid_amount = 0;
        $order_product->total = $total;
        $order_product->tax = 0;
        $order_product->reward = 0;
        $order_product->type = "MarketPlace";
        $order_product->store_name = $store_name;
        $order_product->save();

        $purpletree_order = new PurpleTreeOrder;
        $purpletree_order->seller_id = $store_id;
        $purpletree_order->product_id = $cart_item['product_id'];
        $purpletree_order->order_id = $order_id;
        $purpletree_order->quantity = $cart_item['quantity'];
        $purpletree_order->unit_price = $cart_item['price'];
        $purpletree_order->shipping = $shipping;
        $purpletree_order->total_price = $total;
        $purpletree_order->order_status_id = $order_status_id;
        $purpletree_order->created_at = Carbon::now()->toDateTimeString();
        $purpletree_order->updated_at = Carbon::now()->toDateTimeString();
        $purpletree_order->save();

        }

        $order_status = OrderStatus::where('order_status_id',$order_status_id)->first();

        $orderStatus = $order_status->name;

        $address = Address::where('customer_id',$customer->customer_id)->first();

        $home_address = $address->address_1.' '.$address->address_2;
        $name = $address->firstname.' '.$address->lastname;

        $phone_number = $customer->telephone_countrycode.$customer->telephone;

        $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order->date_added,
            'order_status' => $orderStatus,
          );

        $data['delivery_address']= array(
            'name' => $name,
            'address'=> $home_address,
            'phone_number' => $phone_number
          );

        $data['Order Details']= array([ 
            'Order ID' => $order_id,
            'Order Time' => $order->date_added,
            'Store Name' => $seller->store_name,
            'Game Mode' => $product->buy_mode,
            'Order Status' => $orderStatus,
          ]);

        $data['Delivery Address']= array([
            'Name' => $name,
            'Address'=> $home_address,
            'Phone Number' => $phone_number
          ]);

          $voucher = DB::table('oc_seller_promotions')
                        ->where('seller_id', $product->store_id)
                        ->get();
        
        $voucher->transform(function($i) {
                  return (array)$i;
        });

        $array = $voucher->toArray();

        foreach($voucher as $vouchers){
            $data['Promotion'][]= array([
            'Name' => $vouchers['promotion_name'],
            'End Date' => $vouchers['end_date'],
            'Discount Type' => $vouchers['discount_type'],
            'Discount Amount' => $vouchers['discount_amount'],
            'Code' => $vouchers['promotion_code']
        ]);
        }

          if(!isset($request['discount_price'])) {
            $request['discount_price'] = 0;
        }

         $discount_price =  $request['discount_price'];

         if($product->shipment_free == true){
            $total = $price;
        }else{
            $total = $price + $shipping;
        }


        $items = Product::whereHas('CartList',function($q) use($cart_ids){
            return $q->whereIn('id',$cart_ids);
        })
        ->with(['media'=>function($q){
            $q->select('product_id','image');
        },'CartList'])->get()->toArray();

        // $items->transform(function($i) {
        //         return(array)$i;
        // });

        // $array = $items->toArray();


        foreach($items as $products){
            $data['Product Details'][]= array(
                'Shop Name' => $store_name,
                'Product Image' => !empty($products['media']) ? $products['media'][0]['image'] : '',
                'Product Name' => $products['title'],
                'Variation' => $products['cart_list']['variation_1'].','.$products['cart_list']['variation_2'],
                'Quantity' => $products['cart_list']['quantity'],
                'Subtotal' => $products['cart_list']['total_price'],
                'Shipping Fees' => 0,
                'Promotion' => 0,
            );
        }
        // dd($data['Product Details']);


          // $updateOrderProduct = OrderProduct::find($order_product);
            // $updateOrderProduct->total = $total - $discount_price;
            // $updateOrderProduct->save();
          
        //   return response()->json(['result'=>$data,
        //                            'status' => 'success'], 200);


        foreach ($items as $products)
        {
            $data['product_details'][]=array(
                'store_name' => $store_name,
                'product_image' => !empty($products['media']) ? $products['media'][0]['image'] : '',
                'product_name' =>$products['title'],
                'quantity' => $products['cart_list']['quantity'],
                'price' => $products['cart_list']['price'],
                'discount_price' => 0,
                'variation' => $products['cart_list']['variation_1'].','.$products['cart_list']['variation_2'],
                'shipping' => 0,
                'type' => 'MarketPlace',
                'order_id' => $order_id
            );
        }

        $data['total'] = array(
            'total' => $total
        );

        // $cart = Cart::whereIn('cart_id',$cart_ids)->get();

        //Send Notification to Member
        // $user_unique_id = $user->user_id;

        // $tomorrow = new DateTime('tomorrow');

        //     DB::table('oc_notifications')->insert([
        //         'customer_id' => 0,
        //         'from_customer_id' => 0,
        //         'type' => 'buy_mode',
        //         'notification_title' => 'Successfully purchased an item',
        //         'notification_message' => 'Successfully purchased an item',
        //         'notification_action' => 'buy mode for member',
        //         'notification_interaction' => 'Successfully purchased an item',
        //         'notification_is_read' => 0,
        //         'notification_datetime' => Carbon::now(),
        //         'notification_expire_datetime' => $tomorrow,
        //         'unique_id' => $user_unique_id,
        //         'from_unique_id' => 0
        //     ]);


        return response()->json(['result' => $data,
                                 'status' => 'success'], 200);
    }

    public function reorder($order_cart_list_id)
    {
        $order = OrderCartList::find($order_cart_list_id);

        $reorder = $order->replicate();
        $reorder->order_status = 20;
        $reorder->save();

        return response()->json(['message' => 'reordered',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function addToCart(Request $request)
    {
        $user = auth()->user();
        $customer = $user;

        $this->validate($request,[
            'variation' => 'required',
            'variation_size' => 'required',
            'quantity' => 'required'
        ]);

        $quantity = $request->input('quantity');

        $variation = $request->input('variation');

        $variation_size = $request->input('variation_size');

        $product_id = $request->get('product_id');

        // $product = DB::table('oc_streams')
        //             ->select('oc_product_session.session_id','oc_purpletree_vendor_stores.store_name','oc_streams.title','oc_product.product_id','oc_product.price','oc_product.image','oc_product_variation.value','oc_product_session.bid_type','oc_streams.category','oc_category_description.name', 'oc_product_description.name','oc_product.shipping','oc_product_description.description','oc_product_session.timer_start', 'oc_product_session.timer_end','oc_product_description.meta_description','oc_purpletree_vendor_stores.seller_id')
        //             ->leftJoin('oc_category','oc_category.category_id','=','oc_streams.category')
        //             ->leftJoin('oc_category_description','oc_category_description.category_id','=','oc_category.category_id')
        //             ->leftJoin('oc_product_description','oc_product_description.product_id','=','oc_streams.active_product')
        //             ->leftJoin('oc_product','oc_product.product_id','=','oc_product_description.product_id')
        //             ->leftJoin('oc_product_session','oc_product_session.stream_id','=','oc_streams.id')
        //             ->leftJoin('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_streams.streamer_id')
        //             ->leftJoin('oc_product_variation','oc_product_variation.product_id','=','oc_product.product_id')
        //             ->where('oc_category_description.language_id',1)
        //             ->where('oc_product_session.bid_type',$bid_type)
        //             ->where('oc_product.product_id',$product_id)
        //             ->where('oc_streams.id',$id)
        //             ->first();

        $product = DB::table('oc_product')
                        ->select('oc_product.*','oc_product_shipment.*','oc_product_shipment_region.*')
                        ->join('oc_product_shipment','oc_product_shipment.product_id','=','oc_product.product_id')
                        ->join('oc_product_shipment_region','oc_product_shipment_region.shipment_id','=','oc_product_shipment.id')
                        ->where('oc_product.product_id',$product_id)
                        ->get()
                        ->toArray();

        $shipping = $product->shipping;
        $price = $product->price;
        $total = $price * $quantity + $shipping;

        if(!isset($request['api_id'])){
            $request['api_id'] = 0;
        }

        if(!isset($request['recurring_id'])){
            $request['recurring_id'] = 0;
        }

        if(!isset($request['recurring_id'])){
            $request['recurring_id'] = 0;
        }

        if(!isset($request['option'])){
            $request['option'] = "";
        }

        if(!isset($request['build_tag'])){
            $request['build_tag'] = "";
        }

        if(!isset($request['cart_deposit'])){
            $request['cart_deposit'] = 0;
        }

        if(!isset($request['cart_forfeit'])){
            $request['cart_forfeit'] = 0;
        }

        $cart = new Cart;
        $cart->api_id = $request['api_id'];
        $cart->customer_id = $user->customer_id;
        $cart->session_id = Session::getId();
        $cart->product_id = $product_id;
        $cart->recurring_id = $request['recurring_id'];
        $cart->option = $request['option'];
        $cart->build_tag = $request['build_tag'];
        $cart->type = $product->buy_mode;
        $cart->quantity = $quantity;
        $cart->cart_shipping = $shipping;
        $cart->cart_deposit = $request['cart_deposit'];
        $cart->cart_forfeit = $request['cart_forfeit'];
        $cart->cart_sub_total = $price;
        $cart->cart_total = $price * $quantity + $shipping;
        $cart->date_added = Carbon::now()->toDateTimeString();
        $cart->store_name = $product->store_name;
        $cart->save();

        $cart_id = $cart->id;

        if(!isset($request['discount_price'])) {
            $request['discount_price'] = 0;
        }

        $discount_price =  $request['discount_price'];

          if($discount_price != 0)
          {
            $updateCartPrice = Cart::find($cart_id);
            $updateCartPrice->total = $total - $discount_price;
            $updateCartPrice->save();
          }

        //Send Notification to Member
        $tomorrow = new DateTime('tomorrow');
        $user_unique_id = $user->user_id;

            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'type' => 'buy_mode',
                'notification_title' => "Your order has been added to cart" ,
                'notification_message' => "Your order has been added to cart" ,
                'notification_action' => 'buy mode member',
                'notification_interaction' => "Your order has been added to cart" ,
                'notification_is_read' => 0,
                'notification_datetime' => Carbon::now(),
                'notification_expire_datetime' => $tomorrow,
                'unique_id' => $user_unique_id,
                'from_unique_id' => 0
            ]);

          return response()->json(['status' => 'success'], 200);  
    }

    // public function myCart(Request $request)
    // {
    //     $user = auth()->user();
    //     $customer_id = $user->customer_id;

    //     $cart_orders = DB::table('oc_cart')
    //                     ->select('oc_product_description.name', 'oc_product.image', 'oc_cart.type', 'oc_cart.quantity', 'oc_cart.cart_total','oc_cart.store_name')
    //                     ->join('oc_product','oc_product.product_id','=','oc_cart.product_id')
    //                     ->join('oc_product_description','oc_product_description.product_id','=','oc_product.product_id')
    //                     ->where('oc_cart.customer_id',$customer_id)
    //                     ->orderBy('oc_cart.date_added','desc')
    //                     ->get();

    //     $cart_orders->transform(function($i) {
    //             return (array)$i;
    //     });

    //     $array = $cart_orders->toArray();

    //     foreach ($cart_orders as $cart_order)
    //     {
    //         $data['cart_order'][]=array(
    //             'store_name' => $cart_order['store_name'],
    //             'product_image' => $cart_order['image'],
    //             'product_name' => $cart_order['name'],
    //             'quantity' => $cart_order['quantity'],
    //             'cart_total' => $cart_order['cart_total'],
    //             'type' => $cart_order['type']
    //         );
    //     }

    //     return response()->json(['order' => $data,
    //                              'status' => 'success'],200);
    // }
}

