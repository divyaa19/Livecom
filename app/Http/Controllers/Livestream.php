<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\oc_livestream;
use App\Models\oc_stream;
use App\Models\oc_stream_product_details;
use App\Models\oc_stream_product_bid_amount;
use Illuminate\Support\Carbon;



use Illuminate\Support\Facades\Session;


class Livestream extends Controller
{

    public function startstreambysellersession(Request $request)
    {

        $user = auth()->user();
        // $seller_id = $user->customer_id;\
        $seller_id=1000;
       


        $this->validate($request,[
          'product_id' => 'required',
          'mode_id' => 'required',
          'starting_price' => 'required',
          'start_date' => 'required',
          'start_time' => 'required',
          'end_time' => 'required',
          'end_date' => 'required',


      ]);
        Session::put('live_stream', [
          'product_id' => $request->input('product_id'),
          'mode_id' => $request->input('mode_id'),
          'starting_price'=>$request->input('starting_price'),
          'start_date'=> $request->input('start_date'),
          'end_date'=> $request->input('end_date'),
          'end_time'=> $request->input('end_time'),
          'seller_id'=>$seller_id,

          'start_time' => $request->input('start_time'),
          'session_id' => Session::getId()
      ]);

      $time=DB::table('oc_seller_products_news')
      ->leftjoin('oc_modes','oc_seller_products_news.mode_id','=','oc_modes.id')
      ->where('oc_seller_products_news.id','=',Session::get('live_stream.product_id'))->first('start_date');

      if($time=Session::get('live_stream.start_date')){

      $stream_id=new oc_stream;
      $stream_id->session=Session::get('live_stream.session_id');
      $stream_id->customer_id=$seller_id;
      $stream_id->save();
      $s_id=DB::table('oc_stream')->where('session','=',Session::get('live_stream.session_id'))->where('customer_id','=',$seller_id)->first('id');


      return response()->json(['stream'=> $stream_id,
      'status' => 'success'],200);
     
      }else{
        return response()->json([
      'status' => 'failed'],400);
      }
     
      

    }

    public function getcustomerupdate()
    {
      $user = auth()->user();
      // $seller_id = $user->customer_id;\
      $customer_id=890;


      $prod_id=Session::get('live_stream.product_id');
      $s_id=DB::table('oc_stream')->where('session','=',Session::get('live_stream.session_id'))->first('id');
      $stream_id=$s_id->id;

      $bids_all=DB::table('oc_stream_product_bid_amounts')
      ->join('oc_customer','oc_customer.customer_id','=','oc_stream_product_bid_amounts.user_id')
      ->where('oc_stream_product_bid_amounts.product_id','=',$prod_id)
      ->where('oc_stream_product_bid_amounts.stream_id','=',$stream_id)->get();


       return response()->json([$bids_all],200);
    }
    public function customerupdate(Request $request)
    {
      $this->validate($request,[

        'start_date' => 'required',
        'current_time' => 'required',

    ]);
    $seller_id=Session::get('live_stream.seller_id');
    $s_id=DB::table('oc_stream')->where('session','=',Session::get('live_stream.session_id'))->where('customer_id','=',$seller_id)->first('id');
    $s=$s_id->id;

    $a=$request->input('start_date');
    $b=strtotime($request->input('current_time'));
    $c=Session::get('live_stream.start_date');
    $d=strtotime(Session::get('live_stream.start_time'));
    $e=strtotime(Session::get('live_stream.end_time'));
    $p_id=Session::get('live_stream.product_id');

if($b<=$e){
      $stream_product_bids=new oc_stream_product_bid_amount;
      $stream_product_bids->stream_id=$s;
      $stream_product_bids->product_id=(int)$p_id;
      $stream_product_bids->user_id=$request->input('user_id');
      $stream_product_bids->amount=$request->input('amount');
      $stream_product_bids->deposit=$request->input('deposit');
      $stream_product_bids->quantity=$request->input('quantity');
      $stream_product_bids->winner=$request->input('winner');
      $stream_product_bids->is_paid=$request->input('is_paid');
      $stream_product_bids->is_bid_fast=$request->input('is_bid_fast');
      $stream_product_bids->is_epraise=$request->input('is_epraise');
      $stream_product_bids->timestamp=$b;
      $stream_product_bids->save();

      return response()->json([
        'status' => 'success'],200);
      }else if($e>$b){
        return response()->json([
          'status' => 'session ended'],300);

      }else{
        return response()->json([
          'status' => 'falied'],400);

      }

    }




    public function customerwinnerupdate(Request $request)
    {
      $s_id=DB::table('oc_stream')->where('session','=',Session::get('live_stream.session_id'))->where('customer_id','=',$seller_id)->first('id');
    
      $stream_update_winner=DB::table('oc_stream_product_bids')
                            ->where('user_id','=',$request->input('user_id'))
                            ->where('session_id','=',$s_id)
                            ->update([
                              'winner'=>1,
                            'amount'=>$request->input('winner_amount'),
                            ]);

      return response()->json([
        'status' => 'success'],200);
    }
   

    public function addstreambyseller(Request $request)
    {
      $user = auth()->user();
      $seller_id = $user->customer_id;

      $s_id=DB::table('oc_stream')->where('session','=',Session::get('live_stream.session_id'))->where('customer_id','=',$seller_id)->first('id');


      $stream_product=new oc_stream_product_details;

      $stream_product->product_id=Session::get('live_stream.product_id');
      $stream_product->session_id=$s_id;
      $stream_product->bid_type=$request->input('bid_type');
      $stream_product->sku=$request->input('sku');
      $stream_product->quantity=$request->input('quantity');
      $stream_product->reserve_price=$request->input('reserve_price');
      $stream_product-> forfeit_rate=$request->input('forfeit_rate');
      $stream_product->cutoff_price=$request->input('cutoff_price');
      $stream_product->price_tick=$request->input('price_tick');
      $stream_product->tick_time=$request->input('tick_time');
      $stream_product->tick_type=$request->input('tick_type');
      $stream_product->curent_price=$request->input('current_price');
      $stream_product->max_bid=$request->input('max_bid');
      $stream_product->bid_open=$request->input('bid_open');
      // $stream_product->run_time=$request->input('run_time');
      $stream_product->bids_per_click=$request->input('bid_per_click');
      $stream_product->bid_letters=$request->input('bid_letters');
      $stream_product->timer_start=$request->input('timer_start');
      $stream_product->timer_end=$request->input('timer_end');
      $stream_product->is_deleted=$request->input('is_deleted');
      $stream_product->save();

      return response()->json([
      'status' => 'success'],200);

    }

    public function endsession(Request $request)
    {
      Session::forget('live_stream');

    }


}