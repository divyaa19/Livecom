<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\GameMode;
use App\Models\Stream;
use App\Models\Customer;
use App\Models\BidType;
use DB;
use App\Repository\Product\ProductInterface;
use Illuminate\Support\Facades\Input;


class HomeController extends Controller
{

    public ProductInterface $product;


    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function get_game_mode(Request $request){

        $game = BidType::where([
            'bid_type' => $request['game_mode']
        ])->get();

        return response()->json($game);
    }

    // public function live_store(Request $request){

    //     $result = DB::table('oc_stream_product_details')
    //                 ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as streamer_name"),'oc_stream_product_details.stream_id', 'oc_stream_product_details.bid_type', 'oc_streams.thumbnail as streams_thumbnail', 'oc_streams.viewers_count as viewers' ,'oc_streams.title as title')
    //                 ->leftJoin('oc_streams','oc_streams.streamer_id','=','oc_stream_product_details.stream_id')
    //                 ->leftJoin('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_streams.streamer_id')
    //                 ->orderBy('viewers','desc')
    //                 ->where('oc_streams.status','>',0)
    //                 ->distinct()
    //                 ->get();

    //     return response()->json($result);
    // }

    public function live_store_product(Request $request)
    {
        $product = DB::table('oc_product')
                        ->select('oc_product.*')
                        ->where('sell_mode','livestore')
                        ->simplePaginate($request->limit);

        return response()->json(['product' => $product,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function live_store_product_by_category(Request $request)
    {
        $product = DB::table('oc_product')
                        ->select('oc_product.*')
                        ->where('buy_mode',$request['buy_mode'])
                        ->where('sell_mode','livestore')
                        ->simplePaginate($request->limit);

        return response()->json(['product' => $product,
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function live_store_stream(Request $request){

        $result = DB::table('oc_stream_product_details')
                    ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as streamer_name"),'oc_stream_product_details.stream_id', 'oc_stream_product_details.bid_type', 'oc_streams.thumbnail as streams_thumbnail', 'oc_streams.viewers_count as viewers','oc_streams.title as title')
                    ->leftJoin('oc_streams','oc_streams.streamer_id','=','oc_stream_product_details.stream_id')
                    ->leftJoin('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_streams.streamer_id')
                    ->orderBy('viewers','desc')
                    ->where('bid_type', $request['game_mode'])
                    ->where('oc_streams.status','>',0)
                    ->distinct()
                    ->simplePaginate($request->limit);

        return response()->json($result);
    }

    public function live_entertainment(Request $request){

        $result = DB::table('oc_stream_product_details')
                    ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as streamer_name"), 'oc_stream_product_details.stream_id', 'oc_streams.thumbnail as stream_thumbnail', 'oc_streams.viewers_count as viewers', 'oc_streams.title as title', 'oc_stream_product_details.bid_type')
                    ->join('oc_streams','oc_streams.streamer_id','=','oc_stream_product_details.stream_id')
                    ->join('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_streams.streamer_id')
                    ->where('oc_streams.status','>',0)
                    ->where('oc_streams.live',1)
                    ->orderBy('viewers','desc')
                    ->simplePaginate($request->limit);

        // dd($result);

        // $streamUnique = $result->unique('stream_id');

        // dd($streamUnique);

        return response()->json($result);
    }

    public function live_selling(Request $request){

        $result = DB::table('oc_stream_product_details')
                    ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as streamer_name"),'oc_stream_product_details.stream_id', 'oc_streams.thumbnail as streams_thumbnail', 'oc_streams.viewers_count as viewers','oc_streams.title as title','oc_streams.status')
                    ->leftJoin('oc_streams','oc_streams.streamer_id','=','oc_stream_product_details.stream_id')
                    ->leftJoin('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_streams.streamer_id')
                    ->orderBy('viewers','desc')
                    ->where('oc_streams.status','>',0)
                    ->where('oc_streams.live',1)
                    ->distinct()
                    ->simplePaginate($request->limit);

        // dd($result);

        // $streamUnique = $result->unique('stream_id');

        // dd($streamUnique);

        return response()->json($result);
    }

    // public function live_store_product(Request $request){

    // $result = DB::table('oc_stream_product_details')
    //                 ->select('oc_product.image','oc_product.price','oc_product_description.name','oc_stream_product_details.bid_type','oc_stream_product_details.reserve_price','oc_stream_product_details.cutoff_price')
    //                 ->join('oc_product','oc_product.product_id','=','oc_stream_product_details.product_id')
    //                 ->leftJoin('oc_product_description','oc_product_description.product_id','=','oc_product.product_id')
    //                 ->where('oc_stream_product_details.bid_type', $request['game_mode'])
    //                 ->simplePaginate($request->limit);
    // // dd($result);
    // return response()->json($result);
    // }

    public function product(Request $request){

    $results = DB::table('oc_product_session')
                    ->select('oc_product.image', 'oc_product_session.session_price','oc_product_session.bid_type')
                    ->join('oc_product','oc_product.product_id','=','oc_product_session.product_id')
                    ->where('oc_product_session.bid_type', $request['game_mode'])
                    ->where('oc_product_session.status','=',1)
                    ->get();
    
    return response()->json($results);
    }

    // public function searchProduct(Request $request) {
        
    //     $this->validate($request,[
    //         'search' => 'required'
    //     ]);
    //     $search = '%'.$request->get('search').'%';

    //     $product = DB::table('oc_product')
    //                     ->select('oc_product.*','oc_product_image.*')
    //                     ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
    //                     ->where('title', 'LIKE', $search)
    //                     ->orWhere('description','LIKE',$search)
    //                     ->orWhere('store_id','LIKE',$search)
    //                     ->simplePaginate($request->limit);

    //     // dd($product);
        
    //     if (count($product) > 0){

    //     return response()->json(
    //         [
    //             'product' => $product,
    //             'status'=>'success'
    //         ], 200);
    //     }

    //     return response()->json('No Result Found');
    // }


    public function searchProduct(Request $request): \Illuminate\Http\JsonResponse
    {   

        $search = '%'.$request['search'].'%';

        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->search($search),
        ]);
    }

    public function searchSeller(Request $request) {
        
        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request->get('search').'%';

        $seller_name  = DB::table('oc_purpletree_vendor_stores')
                        ->select('*')
                        ->where('username', 'LIKE', $search)
                        ->orWhere('store_name','LIKE',$search)
                        ->get();

        // dd($seller_name);
        
        if (count($seller_name) > 0){

        return response()->json(
        [
            'seller' => $seller_name,
            'status'=>'success'
        ], 200);

        }

        return response()->json(['message' => 'result not found',
                                 'status' => 'failed'], 404);
    }

    public function searchMember(Request $request) {
        
        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request->get('search').'%';

        $customer_name  = Customer::select(
            "customer_id",
            "customer_group_id",
            "store_id",
            "language_id",
            "firstname",
            "lastname",
            "username",
            "nickname",
            "email",
            "telephone_countrycode",
            "telephone",
            "fax",
            "password",
            "profile_url",
            "salt",
            "cart",
            "wishlist",
            "newsletter",
            "address_id",
            "custom_field",
            "ip",
            "status",
            "email_verified",
            "safe",
            "token",
            "referral_token",
            "pin",
            "code",
            "is_blocked",
            "warning_level",
            "block_live_stream",
            "facebook_id",
            "date_added",
            "is_delete",
            "gender",
            "praise_popup",
            "language",
            "select_language_id",
            "google_id",
            "apple_id",
            "referred_by",
            "lastlogin_datetime",
            "user_id as unique_id",
            "ref_link",
            "refer_id",
        )
                        ->where('username', 'LIKE', $search)
                        ->orWhere('firstname','LIKE',$search)
                        ->orWhere('nickname','LIKE',$search)
                        ->get();

        // dd($seller_name);
        
        if (count($customer_name) > 0){

        return response()->json(
        [
            'customer' => $customer_name,
            'status'=>'success'
        ], 200);

        }

        return response()->json(['message' => 'result not found',
                                 'status' => 'failed'], 404);
    }

    public function searchStream(Request $request) {
        
        $this->validate($request,[
            'search' => 'required'
        ]);
        
        $search = '%'.$request->get('search').'%';

        $stream = DB::table('oc_live')
                        ->select('oc_live.*')
                        ->where('title', 'LIKE', $search)
                        ->get();
        
        if (count($stream) > 0){

        return response()->json(
            [
                'stream' => $stream,
                'status'=>'success'
            ], 200);

        }

        return response()->json(['message' => 'result not found',
                                 'status' => 'failed'], 404);
    }
}

