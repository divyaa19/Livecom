<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnReason;
use App\Models\Product;
use App\Models\OrderCartList;
use App\Models\CancelOrder;
use App\Models\ProductReturn;
use App\Models\ReturnStatus;
use App\Models\Refund;
use App\Models\Address;
use DB;
use Carbon\Carbon;
use App\Models\Bank;

class ReturnController extends Controller
{

    public function getReturnReasons()
    {
        $refund_reasons = ReturnReason::all();

        return response()->json(['reasons' => $refund_reasons,
                                 'status' => 'success'], 200);
    }

    public function productReturn(Request $request,$order_cart_list_id)
    {
        $user = auth()->user();
        $customer = $user;

        $this->validate($request,[
            'return_reason' => 'required',
            'image' => 'required',
            'email' => 'required|email'
        ]);

        $return_reason = $request['return_reason'];

        $email = $request['email'];

        $return_reasons = ReturnReason::where('id',$return_reason)->first();

        $reason = $return_reasons->return_reason;
        
        $order_cart = OrderCartList::find($order_cart_list_id);

        $store_name = $order_cart->store_name;
        $order_id = $order_cart->order_id;
        $product_id = $order_cart->product_id;

        $product = DB::table('oc_product')
                        ->select('oc_product.*','oc_product_image.*','oc_product_variations.*')
                        ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                        ->join('oc_product_variations','oc_product_variations.product_id','=','oc_product.product_id')
                        ->where('oc_product.product_id',$product_id)
                        ->first();

        $product_name = $product->title;
        $product_image = $product->image;
        $customer_id = $user->customer_id;

        $description = $request['description'];

        $date = Carbon::now()->format('Y-m-d_H-i-s');

        $product_return = new productReturn;
        $product_return->order_id = $order_id;
        $product_return->order_cart_id = $order_cart_list_id;
        $product_return->product_id = $product_id;
        $product_return->customer_id = $customer->customer_id;
        $product_return->firstname = $customer->firstname;
        $product_return->lastname = $customer->lastname;
        $product_return->email = $email;
        $product_return->telephone = $customer->telephone;
        $product_return->product = $product_name;
        $product_return->model = '-';
        $product_return->quantity = $order_cart->quantity;
        $product_return->opened = 0;
        $product_return->return_reason_id = $return_reason;
        $product_return->return_action_id = 0;
        $product_return->return_status_id = 1;
        $product_return->build_tag = '';
        $product_return->comment = $description;
        $product_return->date_ordered = $order_cart->created_at;
        $product_return->image = $request['image'];
        $product_return->image_2 = $request['image_2'];
        $product_return->image_3 = $request['image_3'];
        $product_return->image_4 = $request['image_4'];
        $product_return->image_5 = $request['image_5'];
        $product_return->image_6 = $request['image_6'];

            $product_return->save();

            // dd($product_return);

            $return_id = $product_return->id;

            $refund = new Refund;
            $refund->order_cancel_id = 0;
            $refund->return_id = $return_id;
            $refund->refund_amount = $order_cart->price - $order_cart->discount_price;
            $refund->status = 1;
            $refund->save();

            $refund_amount =  $refund->refund_amount;

            $data['products'] = array(
                'store_name' => $store_name,
                'product_image' => $product_image,
                'product_name' => $product_name,
                'quantity' => $order_cart->quantity,
                'price' => $order_cart->price,
                'discount_price' => $order_cart->discount_price,
                'variation' => $product->variation_title.','.$product->variation_value,
                'shipping' => $order_cart->shipping,
                'type' => $product->buy_mode
            );

            $data['refund_details'] = array(
                'refund_amount' => $refund_amount,
                'refund_to' => 'Bank Account',
            );

            $data['reason'] = array(
                'reason' => $reason
            );

            $data['email'] = array(
                'email' => $email
            );

            return response()->json(['product' => $data,
                                    'message' => 'return request submitted',
                                    'status' => 'success'], 200);
    
    }

    public function getMyCartReturnRefund($order_cart_list_id)
    {
        $user = auth()->user();
        $customer = $user;

        $product_return = ProductReturn::where('order_cart_id',$order_cart_list_id)->first();

        if($product_return == null){
            return response()->json(['message' => 'result not found',
                                     'status' => 'failed'], 404);
        }

        $return_status_id = $product_return->return_status_id;

        $return_status = ReturnStatus::where('return_status_id',$return_status_id)->first();

        $returnStatus = $return_status->name;

        $orders = DB::table('oc_order_cart_list')
                    ->select('oc_order_cart_list.*','oc_product.*','oc_product_image.image','oc_product_variations.*')
                    ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                    ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                    ->join('oc_product_variations','oc_product_variations.product_id','oc_product.product_id')
                    ->where('oc_order_cart_list.id',$order_cart_list_id)
                    ->first();

        $tracking_number = 'MY12312SA312';
        $ship_time = Carbon::now()->toDateTimeString();

        $order_id = $orders->order_id;

        $order_time = $orders->created_at;
        $payment_time = $orders->updated_at;
        $stream_name = $orders->stream_name;
        $game_mode = $orders->buy_mode;


        $address = Address::where('customer_id',$customer->customer_id)->first();

        $home_address = $address->address_1.' '.$address->address_2;
        $name = $address->firstname.' '.$address->lastname;

        $phone_number = $customer->telephone_countrycode.$customer->telephone;

        $return_status = $returnStatus;

        switch($return_status){
          case 'Pending':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'ship_time' => $ship_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'tracking_number' => $tracking_number,
            'refund_status' => $returnStatus,
            'return_id' => $product_return->return_id
          ); 
          break;

          case 'Complete':
            $data['order_details']= array(
            'order_id' => $order_id,
            'order_time' => $order_time,
            'payment_time' => $payment_time,
            'stream_name' => $stream_name,
            'game_mode' => $game_mode,
            'refund_status' => $returnStatus,
            'return_id' => $product_return->return_id
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
                'type' => $orders->buy_mode,
                'total' => $orders->price - $orders->discount_price
            );

          return response()->json(['result'=>$data,
                                   'status' => 'success'], 200);
    }

    public function getReturnRefundDetails($return_id)
    {

        $user = auth()->user();
        $customer = $user;

        $return_details = ProductReturn::where('return_id',$return_id)->first();

        if($return_details == null){
            return response()->json(['message' => 'result not found',
                                     'status' => 'failed'], 404);
        }

        $order_cart_list_id = $return_details->order_cart_id;

        $orders = DB::table('oc_order_cart_list')
                    ->select('oc_order_cart_list.*','oc_product.*','oc_product_image.image','oc_product_variations.*')
                    ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                    ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                    ->join('oc_product_variations','oc_product_variations.product_id','oc_product.product_id')
                    ->where('oc_order_cart_list.id',$order_cart_list_id)
                    ->first();

        $refund = Refund::where('return_id',$return_id)->first();

        $refund_amount = $refund->refund_amount;

        $return_reason = $return_details->return_reason_id;

        $return_reasons = ReturnReason::where('id',$return_reason)->first();

        $reason = $return_reasons->return_reason;

        $tracking_number = 'MY12312SA312';
        $ship_time = Carbon::now()->toDateTimeString();

        $order_id = $orders->order_id;

        $order_time = $orders->created_at;
        $payment_time = $orders->updated_at;
        $stream_name = $orders->stream_name;
        $game_mode = $orders->type;


        $address = Address::where('customer_id',$customer->customer_id)->first();

        $home_address = $address->address_1.' '.$address->address_2;
        $name = $address->firstname.' '.$address->lastname;

        $phone_number = $customer->telephone_countrycode.$customer->telephone;
        
        $data['order']=array(
            'store_name' => $orders->store_name,
            'product_image' => $orders->image,
            'product_name' => $orders->title,
            'quantity' => $orders->quantity,
            'price' => $orders->price,
            'discount_price' => $orders->discount_price,
            'variation' => $orders->variation_title.','.$orders->variation_value,
            'shipping' => $orders->shipping,
            'type' => $orders->buy_mode,
            'total' => $orders->price - $orders->discount_price
        );

        $data['refund_details'] = array(
            'refund_amount' => $refund_amount,
            'refund_to' => 'Bank Account',
        );

        $data['reason'] = array(
            'reason' => $reason
        );

        $data['images'] =  array(
            'image_1' => $return_details->image,
            'image_2' => $return_details->image_2,
            'image_3' => $return_details->image_3,
            'image_4' => $return_details->image_4,
            'image_5' => $return_details->image_5,
            'image_6' => $return_details->image_6
        );

        $data['description'] = array(
            'description' => $return_details->comment
        );

        $data['email'] = array(
            'email' => $return_details->email
        );

        return response()->json(['product' => $data,
                                'status' => 'success'], 200);
    }
}
