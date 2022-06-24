<?php

namespace App\Http\Controllers;

use App\Models\OrderCartList;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    public function getOrderStatus(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $order_status = $request['order_status'];

        // $products = DB::table('oc_order')
        //             ->select('oc_order_cart_list.store_name', 'oc_order_cart_list.type', 'oc_order_cart_list.customer_id', 'oc_order_product.name', 'oc_product.image', 'oc_order_cart_list.quantity', 'oc_order_cart_list.price')
        //             ->join('oc_order_product','oc_order_product.order_id','=','oc_order.order_id')
        //             ->join('oc_product','oc_product.product_id','=','oc_order_product.product_id')
        //             ->join('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
        //             ->where('oc_order_cart_list.customer_id',$customer_id)
        //             ->where('oc_order.order_status_id',$order_status)
        //             ->orderby('oc_order.date_added','desc')
        //             ->get();

        $orders = DB::table('oc_order_cart_list')
                        ->select('oc_product.*','oc_order_cart_list.*','oc_product_variations.variation_title','oc_product_variations.variation_size','oc_purpletree_vendor_stores.store_name')
                        ->join('oc_product','oc_product.product_id','=','oc_order_cart_list.product_id')
                        ->join('oc_product_variations','oc_product_variations.product_id','=','oc_product.product_id')
                        ->join('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_unique_id','=','oc_product.store_id')
                        ->where('oc_order_cart_list.customer_id',$customer_id)
                        ->where('oc_order_cart_list.order_status',$order_status)
                        ->orderby('oc_order_cart_list.updated_at','desc')
                        ->get();


        $orders->transform(function($i) {
                return (array)$i;
        });

        $array = $orders->toArray();

        if (!$array)
        {
                return response()->json(['message' => 'something went wrong',
                                         'status'=>'failed',
                                         'success' => false
                                        ], 401);    
        }

        foreach ($orders as $order)
        {    
                $data['order'][] = array(
                        'store_name' => $order['store_name'],
                        'type' => $order['type'],
                        'product_name' => $order['product_title'],
                        'product_image' => $order['image'],
                        'unit_price' => $order['price'] * $order['quantity'],
                        'quantity' => $order['quantity'],
                        'variation' => $order['variation_title'].','.$order['variation_size'],
                        'total_price' => $order['price']
                );
        }

        return response()->json(['order'=> $data,
                                 'status' => 'success',
                                 'success' => true
                                ], 200);
    }

    public function orderReceive($order_cart_list_id)
    {
        $order_received = OrderCartList::find($order_cart_list_id);
        $order_received->order_status = 5;
        $order_received->delivered_at = Carbon::now()->toDateTimeString();
        $order_received->save();

        //Send Notification Promotion to Member
        $user = auth()->user();
        $user_unique_id = $user->user_id;

        $tomorrow = new DateTime('tomorrow');

            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'type' => 'activities',
                'notification_title' => 'Leave a product review' ,
                'notification_message' => 'Leave a product review' ,
                'notification_action' => 'activities for member',
                'notification_interaction' => 'Leave a product review' ,
                'notification_is_read' => 0,
                'notification_datetime' => Carbon::now(),
                'notification_expire_datetime' => $tomorrow,
                'unique_id' => $user_unique_id,
                'from_unique_id' => 0
            ]);

        return response()->json(['message' => 'updated',
                                 'status' => 'success',
                                 'success' => true
                                ], 200);
    }

    public function getOrderStatusList(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'state' => DB::table('oc_order_status')
                ->select('order_status_id', 'name')
                ->where('language_id',1)
                ->get()
        ]);
    }

}
