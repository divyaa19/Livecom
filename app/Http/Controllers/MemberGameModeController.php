<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\SellerProduct;
use App\Models\BidType;
use App\Models\LiveStores;
use App\Models\ProductSession;
use App\Models\LiveStreamsParticipants;
use App\Models\LiveStoreBidHistory;
use Illuminate\Support\Facades\Session;

class MemberGameModeController extends Controller
{
    public function productListing(Request $request,$seller_id)
    {
        $buy_mode = $request['buy_mode'];
        
        $products = DB::table('oc_product')
                                ->select('oc_product.*','oc_buy_mode_data.*')
                                ->join('oc_buy_mode_data','oc_buy_mode_data.product_id','=','oc_product.product_id')
                                ->where('oc_product.store_id',$seller_id)
                                ->where('oc_product.buy_mode',$buy_mode)
                                ->simplePaginate($request->limit);

        $products->transform(function($i) {
                return (array)$i;
        });

        $array = $products->toArray();

        foreach($products as $product)
        {
            $mode = $product['selling_mode_id'] == '2'?'Live':'LiveStore';
            $data['product'][]=array(
                'product_id' => $product['id'],
                'game_duration' => $product['game_duration'],
                'product_name' => $product['product_title'],
                'starting_price' => $product['starting_price'],
                'cutoff_price' => $product['cutoff_price'],
                'buying_mode' => $mode
            );

            return response()->json($data);
        }
    }

    public function liveStream_ProductInfo(Request $request)
    {
        $product_id = $request['product_id'];
        $live_id = $request['live_id'];

        $product_info = DB::table('oc_product')
                        ->select('oc_product.*')
                        ->where('oc_product.product_id',$product_id)
                        ->first();

        $store_info = DB::table('oc_purpletree_vendor_stores')
                            ->select('oc_purpletree_vendor_stores.*')
                            ->where('seller_unique_id',$product_info->seller_unique_id)
                            ->first();

        $store_name = $store_info->store_name;

        $shipping_fees = DB::table('oc_product')
                        ->select('oc_product.*','oc_product_shipping_options.*')
                        ->join('oc_product_shipping_options','oc_product_shipping_options.product_id','=','oc_product.product_id')
                        ->where('oc_product.product_id',$product_id)
                        ->first();

        // dd($product_info);

        $bid_type = BidType::where('id',$product_info->buying_mode_id)->first();

        // dd($bid_type);

        $product_session = DB::table('oc_product_session')
                                ->select('oc_product_session.*')
                                ->where('product_id',$product_id)
                                ->where('stream_id',$live_id)
                                ->where('bid_type', $bid_type->bid_type)
                                ->first();

        $product_session_id = $product_session->session_id;

        // dd($product_session_id);

        $product_participants_count = DB::table('oc_product_session')
                                            ->select('oc_product_session.*','oc_stream_product_participants.*')
                                            ->join('oc_stream_product_participants','oc_stream_product_participants.session_id','=','oc_product_session.session_id')
                                            ->where('oc_product_session.stream_id',$live_id)
                                            ->get()
                                            ->count();

        $bidders = DB::table('oc_product_session')
                            ->select('oc_stream_product_bids.id', 'oc_product_session.product_id', 'oc_stream_product_bids.user_id', 'oc_stream_product_bids.amount', 'oc_customer.username', 'oc_customer.profile_url')
                            ->join('oc_stream_product_participants','oc_stream_product_participants.session_id','=','oc_product_session.session_id')
                            ->join('oc_stream_product_bids','oc_stream_product_bids.session_id','=','oc_product_session.session_id')
                            ->join('oc_customer','oc_customer.customer_id','=','oc_stream_product_bids.user_id')
                            ->where('oc_stream_product_participants.session_id',$product_session_id)
                            ->distinct()
                            ->get();

        // dd($participants);

        $bidders->transform(function($i) {
                return (array)$i;
        });

        $array = $bidders->toArray();

        if (!$array)
        {
                return response()->json(['message' => 'No Bidders Yet',
                                        'status'=>'success'
                                        ], 200);
        }
    

        // dd($participants);
        
        foreach ($bidders as $bidder)
        {
            $data['participant'][]=array(
                'participant_name' => $bidder['username'],
                'profile_image' => $bidder['profile_url'],
                'bid_amount' => 'RM'.$bidder['amount']
            );
        }

        return response()->json(['product_info' => $product_info,
                                 'store_name' => $store_name,
                                 'participant_count' => $product_participants_count,
                                 'bidders' => $data,
                                 'shipping' => $shipping_fees,
                                 'status' => 'success'], 200);
    }

    public function addLiveStoreParticipants(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $product_id = $request['product_id'];

        $this->validate($request, [
            'product_id' => 'required',
            ]);
        
        $product = SellerProduct::where('id',$product_id)->first();

        $bid_id = $product->buying_mode_id;

        $bid_type = BidType::where('id',$bid_id)->first();

        $type = $bid_type->bid_type;

        $product_session = ProductSession::where('product_id',$product_id)
                                            ->where('bid_type',$type)
                                            ->first();

        $participants = new LiveStores;
        $participants->customer_id = $customer_id;
        $participants->session_id = $product_session;
        $participants->product_id = $product_id;
        $participants->type = $type;
        $participants->status = 1;
        $participants->save();

        return response()->json(['participant_id' => $participants->id,
                                 'message' => 'joined',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function addLiveStreamParticipants(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $product_id = $request['product_id'];

        $live_id = $request['live_id'];

        $this->validate($request, [
            'product_id' => 'required'
            ]);
        
        $live_product = SellerProduct::where('id',$product_id)
                                        ->where('selling_mode_id',2)
                                        ->first();

        $bid_id = $live_product->buying_mode_id;

        $bid_type = BidType::where('id',$bid_id)->first();

        $type = $bid_type->bid_type;

        $product_session = ProductSession::where('product_id',$product_id)->first();

        $product_session_id = $product_session->session_id;

        $participants = new LiveStreamsParticipants;
        $participants->user_id = $customer_id;
        $participants->session_id = $product_session_id;
        $participants->bid_type = $type;
        $participants->winner = 0;
        $participants->save();

        return response()->json(['participant_id' => $participants->id,
                                 'message' => 'joined',
                                 'status' => 'success',
                                 'success' => true], 200);
    }

    public function auction_low_GrabItButton()
    {
        $product_id = $request['product_id'];

        $product = SellerProduct::where('id',$product_id)->first();

    }

    public function livestore_BidNowButton(Request $request,$participant_id)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        
        $product_id = $request['product_id'];

        $store_product = SellerProduct::where('id',$product_id)
                                        ->where('selling_mode_id',1)
                                        ->where('buying_mode_id',1)
                                        ->first();

        if(!$store_product){
            return response()->json(['message' => 'something went wrong',
                                     'status' => 'failed'], 404);
        }

        $bid_id = $store_product->buying_mode_id;

        $bid_type = BidType::where('id',$bid_id)->first();

        $type = $bid_type->bid_type;

        // dd($type);

        $bid_amount = $request['bid_amount'];

        $product_session = ProductSession::where('product_id',$product_id)
                                            ->where('bid_type',$type)
                                            ->first();

        $bid_session = $product_session->session_id;

        $bidded_amount = LiveStoreBidHistory::where('bid_session_id',$bid_session)->first();

        if($bid_amount == $bidded_amount){
            return response()->json(['message' => 'please enter more',
                                     'status' => 'failed'], 400);
        }

        $user_bid = new LiveStoreBidHistory;
        $user_bid->bid_session_id = $bid_session;
        $user_bid->customer_id = $customer_id;
        $user_bid->amount = $bid_amount;
        $user_bid->product_id = $product_id;
        $user_bid->quantity = 1;
        $user_bid->save();

        return response()->json(['message' => 'bid placed',
                                 'status' => 'success',
                                 'success' => true], 200);

    }
}
