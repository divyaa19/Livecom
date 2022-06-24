<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Neonexxa\BillplzWrapperV3\BillplzBill;
use Neonexxa\BillplzWrapperV3\BillplzCollection;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderCartList;
use App\Models\OrderProduct;
use App\Models\UpdateWallet;
use App\Models\Wallet;
use App\Models\UpdatePaymentBill;
use App\Models\BidTransaction;
use App\Models\ReloadItem;
use App\Models\PurpleTreeOrder;
use App\Models\PurpleTreeStore;
use App\Models\ShopBalance;
use Carbon\Carbon;
use DB;

class BillplzController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
        'returnstatus'
        ]]);
    }
    public function createBillplzCollection()
    {
        $res = new BillplzCollection;
        $res->title = "Livecom Collection";
        $res = $res->create_collection();
        list($rheader,$rurl) = explode("\n\r\n", $res);
        $bplz_result = json_decode($rurl);
    }


    public function ReloadBundles()
    {
        $reloadBundles = ReloadItem::where('status','active')->get();

        foreach ($reloadBundles as $reloadBundle) 
        {
            $data['Reload Amount'][] = array(
                'value' => $reloadBundle['value'],
				'price'      => number_format($reloadBundle['price'],2,".",","),
            );
        }

        return response()->json($data);
        
    }

    public function createPaymentBill(Request $request,$order_id)
    {
        $user = auth()->user();

        $customer = $user;

        $callbackURL = getenv('CALLBACK_URL');
        $redirectURL = getenv('REDIRECT_URL');

        $selectedProduct= OrderProduct::where('order_id',$order_id)->first();

        $order = Order::where('order_id',$order_id)->first();

        if($order === null)
        {
            $gamemode = null;
        }
        else
        {


        // dd($order);

        $gamemode = $order->type;
         }

        switch($gamemode) {
            case('auction_high'):
                
                $description = 'LiveCom Auction High';
                $transaction_type = 13;

                break;
 
            case('auction_low'):

                $description = 'LiveCom Auction Low';
                $transaction_type = 14;

                break;

            case('marketplace'):
                
                $description = 'LiveCom MarketPlace';
                $transaction_type = 16;

                break;
        }

        $orders = OrderProduct::where('order_id',$order_id)->get()->toArray();

        // dd($orders);

        // dd($selectedProduct);

        if ($selectedProduct != NULL)
        {
            //Create Bill
                $bill = new BillplzBill;
                $bill->collection_id = getenv('BILLPLZ_COLLECTION_ID');
                $bill->description = $description;
                $bill->email = $customer->email;
                $bill->mobile = $customer->telephone_countrycode.$customer->telephone;
                $bill->name = $customer->firstname;
                $bill->amount = $selectedProduct->total * 100;
                $bill->callback_url = $callbackURL;
                $bill->redirect_url = $redirectURL;
                $bill = $bill->create_bill();
                list($rhead, $rurl) = explode("\n\r\n", $bill);
                $bplz_result = json_decode($rurl);

                $bill_url = $bplz_result->url;

                foreach($orders as $order)
                {
                    $order_status = $order['type'];

                    switch($order_status){
                    case 'marketplace':
                        $trans_type = 7;
                        $price = $order['price'];
                    break;

                    case 'auction_high':
                        $trans_type = 7;
                        $price = $order['price'];
                    break;

                    case 'auction_low':
                        $trans_type = 7;
                        $price = $order['price'];
                    break;

                    case 'live_e_commerce':
                        $trans_type = 16; 
                        $price = $order['price'];
                    break;

                    case 'live_auction_high':
                        $trans_type = 13; 
                        $price = $order['price'];
                    break;

                    case 'live_auction_low':
                        $trans_type = 14; 
                        $price = $order['price'];
                    break;

                    }

                    $transaction = new BidTransaction;
                    $transaction->wallet_id = 0;
                    $transaction->amount = $price;
                    $transaction->trans_type = $trans_type;
                    $transaction->product_id = $order['product_id'];
                    $transaction->status = 2;
                    $transaction->trans_direction = 'deb';
                    $transaction->order_id = $order_id;
                    $transaction->note = "";
                    $transaction->user_unique_id = $customer->user_id;
                    $transaction->save();

                    $sellers = PurpleTreeStore::where('store_name',$order['store_name'])->get()->toArray();

                    foreach($sellers as $seller)
                    {
                        $seller_transaction = new BidTransaction;
                        $seller_transaction->wallet_id = 0;
                        $seller_transaction->amount = $price;
                        $seller_transaction->trans_type = $trans_type;
                        $seller_transaction->product_id = $order['product_id'];
                        $seller_transaction->status = 2;
                        $seller_transaction->trans_direction = 'cre';
                        $seller_transaction->order_id = $order_id;
                        $seller_transaction->user_type = 'seller';
                        $seller_transaction->note = "";
                        $seller_transaction->user_unique_id = $seller['seller_unique_id'];
                        $seller_transaction->save();
                    }
                }

                    $payment = new Payment;
                    $payment->payment_bill_id = $bplz_result->id;
                    $payment->payment_amount = $selectedProduct->total;
                    $payment->order_id = $order_id;
                    $payment->payment_is_paid = 0;
                    $payment->date_added = Carbon::now()->toDateTimeString();
                    $payment->date_modified = Carbon::now()->toDateTimeString();
                    $payment->payment_auth_type = 'billplz';
                    $payment->save();

                
                    $respond = array("responseStatus"=>true, "data"=>$bill_url, "responseCode"=>200);
                    return $respond;
        }else{
            $respond = array("responseStatus"=>false, "error"=>'Please select product', "responseCode"=>"FAILED");
            return $respond;
        }
        
    }

    public function returnstatus(Request $request)
    {
        if($request['paid'] == "false")
         return "false";
        else
        $auth_payment = $request->all();

        $auth_payment_id = $auth_payment['billplz']['id'];
        $auth_payment_paid = $auth_payment['billplz']['paid'];
        
        $payment = Payment::where('payment_bill_id',$auth_payment_id)->first();

        // dd($auth_payment);
        
        $paid =$auth_payment_paid =="true"?1:0;

        //Update oc_payment payment_is_paid
        $UpdatePayment = UpdatePaymentBill::find($auth_payment_id);
        $UpdatePayment->payment_auth_response = $auth_payment;
        $UpdatePayment->payment_is_paid = $paid;
        $UpdatePayment->save();



        $Payment_order_id = $UpdatePayment->order_id;
        

        if($auth_payment_paid == 'true')
        {
            $orderData = DB::table('oc_order')
                        ->select('oc_order.order_id', 'oc_order.invoice_no', 'oc_order.invoice_prefix', 'oc_order.date_added', 
                                'oc_order.total', 'oc_order.currency_code', 'oc_order.currency_value', 'oc_order.type', 
                                'oc_order.customer_id', 'oc_order.shipping_firstname', 'oc_order.shipping_lastname', 
                                'oc_order.shipping_company', 'oc_order.shipping_address_1', 'oc_order.shipping_address_2', 
                                'oc_order.shipping_city', 'oc_order.shipping_postcode', 'oc_order.shipping_country', 
                                'oc_order.shipping_zone', 'oc_order.firstname', 'oc_order.lastname', 'oc_order.email', 
                                'oc_order.telephone', 'oc_order.fax', 'oc_order_status.order_status_id as status_id', 
                                'oc_order_status.name as order_status_name')
                        ->leftJoin('oc_order_status','oc_order.order_status_id','=','oc_order_status.order_status_id')
                        ->where('oc_order.order_id',$Payment_order_id)
                        ->where('oc_order_status.language_id',1)
                        ->first();

            $insert_balance = new ShopBalance;

            // dd($orderData);

            // $orderItemData = DB::table('oc_order')
            //                 ->select('oc_order.order_id', 'oc_order_status.order_status_id as status_id', 
            //                         'oc_order_status.name as order_status_name', 'oc_order.date_added', 'oc_order.total', 
            //                         'oc_order.currency_code', 'oc_order.currency_value', 'oc_order.type', 'oc_order_product.product_id', 
            //                         'oc_order_product.order_product_id', 'oc_order_product.quantity as order_product_quantity', 
            //                         'oc_order_product.price as order_product_price', 'oc_order_product.bid_amount', 
            //                         'oc_order_product.quantity', 'oc_order_product.price', 'oc_order_product.total', 
            //                         'oc_product_description.name as product_name', 'oc_product_description.description', 
            //                         'oc_product_description.short_description', 'oc_product.image')
            //                 ->leftJoin('oc_order_status','oc_order.order_status_id','=','oc_order_status.order_status_id')
            //                 ->leftJoin('oc_order_product','oc_order_product.order_id','=','oc_order.order_id')
            //                 ->leftJoin('oc_product_description','oc_product_description.product_id','=','oc_order_product.product_id')
            //                 ->leftJoin('oc_product','oc_product.product_id','=','oc_order_product.product_id')
            //                 ->where('oc_order.order_id',$Payment_order_id)
            //                 ->where('oc_order_status.language_id',1)
            //                 ->orderBy('oc_order.order_id','desc')
            //                 ->first();

                $orderItems = DB::table('oc_order')
                                        ->select('oc_order.*','oc_order_cart_list.*','oc_product.*')
                                        ->join('oc_order_cart_list','oc_order_cart_list.order_id','oc_order.order_id')
                                        ->join('oc_product','oc_product.product_id','oc_order_cart_list.product_id')
                                        ->where('oc_order.order_id',$Payment_order_id)
                                        ->get();

                // dd($orderItems);

                $orderItems->transform(function($i) {
                    return (array)$i;
                });

                $array = $orderItems->toArray();

                $now = Carbon::now();

                foreach($orderItems as $orderItem)
                {
                    $shopBalance = ShopBalance::where('seller_unique_id',$orderItem['store_id'])->first();

                    if(!$shopBalance)
                    {
                        DB::table('oc_seller_balance')->insert([
                            'seller_unique_id' => $orderItem['store_id'],
                            'amount' => $orderItem['price'],
                            // 'product_id' => $orderItem['product_id'],
                            // 'user_unique_id' => $orderItem['customer_id'],
                            // 'store_name' => $orderItem['store_name'],
                            // 'order_id' => $orderItem['order_id'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }else{
                        $balance = $shopBalance->amount + $orderItem['price'];

                        ShopBalance::where('seller_unique_id',$orderItem['store_id'])->update(['amount' => $balance]);
                    }
                
                }
                // dd($orderItem);

                // if($orderItem != 0)
                // {
                //     $selectedProduct= OrderProduct::where('order_id',$Payment_order_id)->first();
                // }

                $order_data = array(
                    'order_id' => $orderData->order_id,
                    'status'  => 4,
                );

                $Order = Order::find($order_data['order_id']);
                $Order->order_status_id = $order_data['status'];
                $Order->date_modified = Carbon::now()->toDateTimeString();
                $Order->save();


                $OrderQuantity = DB::table('oc_product')
                                    ->where('product_id','=',$orderItem)
                                    ->decrement('quantity',1);

                $OrderCart = OrderCartList::where('order_id',$order_data['order_id'])->update(['order_status' => $order_data['status'],
                                                                                               'updated_at' => Carbon::now()->toDateTimeString()]);


                $order_status = PurpleTreeOrder::find($order_data['order_id']);
                $order_status->order_status_id = $order_data['status'];
                $order_status->save();
                
                $updateTransaction = BidTransaction::where('order_id',$Payment_order_id)->update(['status' => 3]);
        }

    }

    public function reloadDetails()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $data = array();

        $wallet = Wallet::where('customer_id',$customer_id)->first();

        if($wallet === null)
        {
            $transaction = array();
        }
        else{
            $walletID = $wallet->wallet_id;

        $transaction = BidTransaction::where('wallet_id',$walletID)
                        ->where('trans_type',6)
                        ->orderBy('transaction_id','desc')
                        ->first();

        $data=[
            'Reload Amount'=> $transaction->amount,
            'Reload To' => 'LiveWallet',
            'Payment Method' => 'Online Banking',
            'Transaction ID' => $transaction->transaction_id,
            'Withdraw From' => 'LiveWallet',
            'Completed Time' => $transaction->date_added
        ];
        }
        

        return $data;
    }

}
