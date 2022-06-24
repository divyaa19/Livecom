<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurpleTreeOrder;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\PurpleTreeProduct;

class ShopActivityController extends Controller
{
    public function getshopactivity_toship(Request $request, $seller_unique_id)
    {

        // $user = auth()->user();

        // $user = auth()->user();
        // $customer_id = $user->customer_id;

        // $seller_unique_id = $request['seller_unique_id'];
        // $customer=666;

        $shop_activity_toship = DB::table('oc_product')
                                    ->select('oc_product.*','oc_order_cart_list.*')
                                    ->leftjoin('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
                                    ->where('oc_order_cart_list.order_status','=',2)   
                                    ->where('oc_product.store_id','=',$seller_unique_id)
                                    ->get();


        $shop_activity_todelivered = DB::table('oc_product')
                                        ->select('oc_product.*','oc_order_cart_list.*')
                                        ->leftjoin('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
                                        ->where('oc_order_cart_list.order_status','=',3)   
                                        ->where('oc_product.store_id','=',$seller_unique_id)
                                        ->get();

        $shop_activity_tocomplete = DB::table('oc_product')
                                        ->select('oc_product.*','oc_order_cart_list.*')
                                        ->leftjoin('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
                                        ->where('oc_order_cart_list.order_status','=',6)   
                                        ->where('oc_product.store_id','=',$seller_unique_id)
                                        ->get();

        return response()->json([
            'toprogress' => $shop_activity_toship,
            'underway' => $shop_activity_todelivered,
            'complete' => $shop_activity_tocomplete,
            ],200);
    }
    public function getshopactivity_soldout($store_id)
    {
        // // $user = auth()->user();
        // $user = auth()->user();
        // // $customer_id = $user->customer_id;
        // $customer=666;

        // $shop_activity_tocomplete = DB::table('oc_purpletree_vendor_products')
        // // PurpleTreeOrder::select('*')
        //                             ->join('oc_product','oc_product.product_id','=','oc_purpletree_vendor_products.product_id')  ->select('*')
        //                             ->where('oc_product.stock_status_id','=',6)
        //                             ->where('oc_purpletree_vendor_products.seller_id','=',$customer_id)
        //                             ->get();

        $shop_activity_soldout = DB::table('oc_product')
                                    ->where('stock_status_id', 5)
                                    ->where('store_id', $store_id)
                                    ->get();

        return response()->json(['sold_out' => $shop_activity_soldout,
                                 'status' => 'success',
                                 'success' => true],200);
    }
    public function restocktheproduct($product_id)
    {
        // $user = auth()->user();

        // $user = auth()->user();
        // $customer_id = $user->customer_id;
        $shop_activity_changeproductid = Product::select('oc_product.*')
                                        ->where('oc_product.product_id','=',$product_id)
                                        ->update(['stock_status_id'=>7]);

        return response()->json([$shop_activity_changeproductid],200);

    }
    public function removetheproduct($product_id)
    {
        // // $user = auth()->user();

        // $user = auth()->user();
        // $customer_id = $user->customer_id;
        // $shop_activity_changestockid= DB::table('oc_product')
        //                             ->where('product_id','=',$product_id)
        //                             ->update(
        //                             [
        //                                 'stock_status_id'=>25,
        //                             ]);
        // return response()->json($shop_activity_changestockid);

        $changestatus = DB::table('oc_product')
                            ->where('product_id', $product_id)
                            ->where('status', 3)
                            ->update([
                                'status' => 10,
                            ]);
                            
         return response(['to_remove' => $changestatus,
                         'status' => 'success'
                        ], 200);
    }

    public function getremovedtheproduct()
    {
        // $user = auth()->user();

        $user = auth()->user();
        $customer_id = $user->customer_id;
        $shop_activity_get_removed_product= DB::table('oc_purpletree_vendor_products')
                                            ->join('oc_product','oc_product.product_id','=','oc_purpletree_vendor_products.product_id')  ->select('*')
                                            ->where('oc_product.stock_status_id','=',25)
                                            ->where('oc_purpletree_vendor_products.seller_id','=',$customer_id)
                                            ->get();
        
        return response()->json($shop_activity_get_removed_product);
    }

    public function deleteremovedtheproduct($product_id)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        $shop_activity_get_removed_product=DB::table('oc_purpletree_vendor_products')
                                                ->where('oc_purpletree_vendor_products.product_id','=',$product_id)
                                                ->join('oc_product','oc_product.product_id','=','oc_purpletree_vendor_products.product_id')  
                                                ->select('*')
                                                ->where('oc_product.stock_status_id','=',25)
                                                ->where('oc_purpletree_vendor_products.seller_id','=',$customer_id)
                                                ->delete();

        return response()->json(['success'=> $shop_activity_get_removed_product], 200);

    }

   
    public function getshopactivity_toremove($seller_unique_id)
    {
        // $user = auth()->user();

        
        $to_remove = DB::table('oc_product')
                        ->select('*')
                        ->where('stock_status_id',10)
                        ->where('store_id',$seller_unique_id)
                        ->get();

        return response(['sold_out' => $to_remove,
                         'status' => 'success'], 200);

    }

    public function trackingnumber(Request $request, $customer_id)
    {
        $this->validate($request, [
            'tracking_number' => 'required',
            'product_id' => 'required'
        ]);

        $now = Carbon::now();

        $tracking_number = DB::table('oc_order_cart_list')
                                ->where('customer_id', $customer_id)
                                ->where('product_id', $request['product_id'])
                                ->where('order_status', 2)
                                ->update([
                                    'order_status' => 3,
                                    'tracking_number' => $request['tracking_number'],
                                    'ship_time' => Carbon::now()
                                ]);
        
        if($tracking_number == 0){
            return response(['message' => 'something went wrong',
                             'status' => 'failed'], 404);
        }

        return response(['message' => 'shipment updated',
                         'status' => 'success'], 200);
    }

    public function get_to_ship(Request $request ,$seller_unique_id)
    {
        $to_ship = DB::table('oc_product')
                        ->select('oc_product.*','oc_order_cart_list.*')
                        ->leftjoin('oc_order_cart_list','oc_order_cart_list.product_id','=','oc_product.product_id')
                        ->where('oc_order_cart_list.order_status','=',1)   
                        ->where('oc_product.store_id','=',$seller_unique_id)
                        ->get()
                        ->count();

        return response(['to_ship' => $to_ship,
                         'status' => 'success'], 200);
    }

    public function get_sold_out($seller_unique_id)
    {
        
        $sold_out = DB::table('oc_product')
                        ->select('*')
                        ->where('stock_status_id',5)
                        ->where('store_id',$seller_unique_id)
                        ->get()
                        ->count();

        return response(['sold_out' => $sold_out,
                         'status' => 'success'], 200);
    }

    public function get_to_remove($seller_unique_id)
    {
        
        $to_remove = DB::table('oc_product')
                        ->select('*')
                        ->where('stock_status_id',10)
                        ->where('store_id',$seller_unique_id)
                        ->get()
                        ->count();

        return response(['to_remove' => $to_remove,
                         'status' => 'success'], 200);
    }    
   
}