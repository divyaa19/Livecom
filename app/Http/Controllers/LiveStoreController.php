<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\LiveStoreProduct;
use App\Events\LiveStoreProductEvent;
use App\Events\WinnerParticipantEvent;
use App\Models\LiveStores;
use Illuminate\Support\Facades\DB;
use Exception;
class LiveStoreController extends Controller
{
	public function store(Request $request){
		$validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'start_on' => 'required',
                'end_on' => 'required'
        ]);
		if($validator->fails()){
                return handleValidation($validator->errors(),'Some Filed are missing');       
        }
        $liveStoreProduct=new LiveStoreProduct;
        $liveStoreProduct->product_id = $request->input('product_id');
        $liveStoreProduct->start_on = $request->input('start_on');
        $liveStoreProduct->end_on = $request->input('end_on');
        $liveStoreProduct->status = 0;
        $liveStoreProduct->date_added = date('Y-m-d H:i:s');

        $liveStoreProduct->save();
        
        $productinfo=Product::with('media')->with('variations')->with('specifications')->with('shipment')->find($request->input('product_id'));
        $productinfo->liveStoreProduct=$liveStoreProduct;
        
        event(new LiveStoreProductEvent($productinfo));
	}
	public function update(Request $request){
		$id=$request->session_id;
		$validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'start_on' => 'required',
                'end_on' => 'required'
        ]);
		if($validator->fails()){
                return handleValidation($validator->errors(),'Some Filed are missing');       
        }
        $liveStoreProduct=LiveStoreProduct::find($id);
        $liveStoreProduct->product_id = $request->input('product_id');
        $liveStoreProduct->start_on = $request->input('start_on');
        $liveStoreProduct->end_on = $request->input('end_on');
        $liveStoreProduct->status = 0;
        $liveStoreProduct->date_modified = date('Y-m-d H:i:s');
        $liveStoreProduct->save();


        $productinfo=Product::with('media')->with('variations')->with('specifications')->with('shipment')->find($request->input('product_id'));
        $productinfo->liveStoreProduct=$liveStoreProduct;
            
        event(new LiveStoreProductEvent($productinfo));
	}
	public function liveStreamOnOff(Request $request){
		$id=$request->session_id;
		$validator = Validator::make($request->all(), [
                'session_id' => 'required',
                'status' => 'required'
        ]);
		if($validator->fails()){
                return handleValidation($validator->errors(),'Some Filed are missing');       
        }
        $liveStoreProduct=LiveStoreProduct::find($id);
        $liveStoreProduct->status = $request->input('status');
        $liveStoreProduct->date_modified = date('Y-m-d H:i:s');
        $liveStoreProduct->save();


        $productinfo=Product::with('media')->with('variations')->with('specifications')->with('shipment')->find($request->input('product_id'));
        $productinfo->liveStoreProduct=$liveStoreProduct;
            
        event(new LiveStoreProductEvent($productinfo));
        if($request->input('status')==0){
        	$winnerParticipantsEvent=LiveStores::where('session_id',$id)->orderBy('score','desc')->limit(1)->first();
        	if(!empty($winnerParticipantsEvent)){
        		event(new WinnerParticipantEvent($winnerParticipantsEvent));
        	}
        	
        }
	}
}