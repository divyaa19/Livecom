<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\LiveStoreProduct;
use App\Models\LiveStoreBidHistory;
use App\Models\LiveStores;
//use App\Events\LivestoreProductParticipantsEvent;
use App\Events\TopParticipantsEvent;
use Illuminate\Support\Facades\DB;
use Exception;
use Auth;
class LivestoreProductParticipants extends Controller
{
	public function store(Request $request){
		$user = auth()->user();
		$session_id=$request->session_id;
		$validator = Validator::make($request->all(), [
                'session_id' => 'required',
                'amount' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
        ]);
        if($validator->fails()){
        	return handleValidation($validator->errors(),'Some Filed are missing');   
        }
        $liveStoreProduct=LiveStoreProduct::find($session_id);

        $LiveStoreBidHistory=new LiveStoreBidHistory;
        $LiveStoreBidHistory->bid_session_id = $liveStoreProduct->session_id;
        $LiveStoreBidHistory->product_id = $liveStoreProduct->product_id;
        $LiveStoreBidHistory->customer_id = $user->id;
        $LiveStoreBidHistory->quantity = $request->input('quantity');
        $LiveStoreBidHistory->amount = $request->input('amount');
        $LiveStoreBidHistory->date_added = date('Y-m-d H:i:s');
        $LiveStoreBidHistory->save();

        $LiveStores=LiveStores::where('session_id',$session_id)->where('customer_id',$user->id)->get()->first();
        if(!empty($LiveStores)){
        	
	        
	        $LiveStores->score = $request->input('amount');
	        $LiveStores->type = 'Live';
	        $LiveStores->status = 1;
	        $LiveStores->date_added = date('Y-m-d H:i:s');
	        $LiveStores->save();
        }else{
        	$LiveStores=new LiveStores;
	        $LiveStores->session_id = $liveStoreProduct->session_id;
	        $LiveStores->product_id = $liveStoreProduct->product_id;
	        $LiveStores->customer_id = $user->id;
	        $LiveStores->type = 'Live';
	        $LiveStores->status = 1;
	        $LiveStores->score = $request->input('amount');
	        $LiveStores->date_added = date('Y-m-d H:i:s');
	        $LiveStores->save();
        }
        
       // event(new LivestoreProductParticipantsEvent($LiveStores));
        $topParticipants=LiveStores::where('session_id',$session_id)->orderBy('score','desc')->limit(3)->get();
        
        event(new TopParticipantsEvent($topParticipants));
	}
	public function TopPaticipants(Request $request){

	}
	public function WinningPaticipant(Request $request){

	}
}