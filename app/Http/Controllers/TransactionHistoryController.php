<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function transaction_history(Request $request)
    {
        //Get current user ID
        $data = [];
        $user = auth()->user();
        $user_id = $user->user_id;

        $transactions = DB::table('oc_bid_transaction')
            ->select('*')
            ->leftJoin('oc_transaction_type', 'oc_bid_transaction.trans_type', '=', 'oc_transaction_type.trans_type_id')
            ->leftJoin('oc_transaction_status', 'oc_bid_transaction.status', '=', 'oc_transaction_status.status_id')
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->orderBy('oc_bid_transaction.date_added', 'desc')
            ->simplePaginate($request->limit);

        $transactions->transform(function ($i) {
            return (array)$i;
        });

        $array = $transactions->toArray();

        foreach ($transactions as $transaction) {
            $symbol = $transaction['trans_direction'] == 'deb' ? '-' : '';
            $data['transactions'][] = array(
                'amount' => $transaction['amount'],
                'bank_account_no' => $transaction['bank_account_number'],
                'bank_name' => $transaction['bank_name'],
                'trans_direction' => $transaction['trans_direction'],
                'trans_type_id' => $transaction['trans_type_id'],
                'date_added' => $transaction['date_added']
            );
        }


        return $data;
    }

    public function transferIn(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        //Get Wallet ID with given Customer ID
        $wallet_table = DB::table('oc_wallet')
                        ->select('*')
                        ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                        ->where('oc_customer.customer_id', $customer_id)
                        ->first();

        $walletID = $wallet_table->wallet_id;

        $transactions = DB::table('oc_bid_transaction')
                        ->select('oc_bid_transaction.date_added','amount','trans_direction','transaction_id','oc_customer.firstname')
                        ->leftJoin('oc_transaction_type','oc_bid_transaction.trans_type','=','oc_transaction_type.trans_type_id')
                        ->leftJoin('oc_transaction_status','oc_bid_transaction.status','=','oc_transaction_status.status_id')
                        ->leftJoin('oc_wallet', 'oc_wallet.wallet_id', 'oc_bid_transaction.ref_wallet')
                        ->leftJoin('oc_customer', 'oc_wallet.customer_id', 'oc_customer.customer_id')
                        ->where('oc_bid_transaction.wallet_id',$walletID)
                        ->where('oc_bid_transaction.trans_direction','cre')
                        ->where('oc_bid_transaction.status',3)
                        ->orderBy('oc_bid_transaction.date_added','desc')
                        ->simplePaginate($request->limit);

        $transactions->transform(function($i) {
                return (array)$i;
        });

        $array = $transactions->toArray();

        if (!$array){

                return response()->json('Transaction Not Found');

        }

        foreach ($transactions as $transaction) {
                $symbol = $transaction['trans_direction'] == 'deb'?'-':'';
                $data['transactions'][] = array(
                        'Completed Time' => $transaction['date_added'],
                        'Transaction ID' => $transaction['transaction_id'],
                        'Amount'        => $symbol . '' . $transaction['amount'],
                        'From'          => $transaction['firstname']
                );
        }

        return response()->json($data);
    }

    public function transferOut(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        //Get Wallet ID with given Customer ID
        $wallet_table = DB::table('oc_wallet')
                        ->select('*')
                        ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                        ->where('oc_customer.customer_id', $customer_id)
                        ->first();

        $walletID = $wallet_table->wallet_id;

        $transactions = DB::table('oc_bid_transaction')
                        ->select('oc_bid_transaction.date_added','amount','trans_direction','transaction_id','oc_customer.firstname')
                        ->leftJoin('oc_transaction_type','oc_bid_transaction.trans_type','=','oc_transaction_type.trans_type_id')
                        ->leftJoin('oc_transaction_status','oc_bid_transaction.status','=','oc_transaction_status.status_id')
                        ->leftJoin('oc_wallet', 'oc_wallet.wallet_id', 'oc_bid_transaction.ref_wallet')
                        ->leftJoin('oc_customer', 'oc_wallet.customer_id', 'oc_customer.customer_id')
                        ->where('oc_bid_transaction.wallet_id',$walletID)
                        ->where('oc_bid_transaction.trans_direction','deb')
                        ->where('oc_bid_transaction.status',3)
                        ->orderBy('oc_bid_transaction.date_added','desc')
                        ->simplePaginate($request->limit);

        $transactions->transform(function($i) {
                return (array)$i;
        });

        $array = $transactions->toArray();

        if (!$array){

                return response()->json('Transaction Not Found');

        }

        foreach ($transactions as $transaction) {
                $symbol = $transaction['trans_direction'] == 'deb'?'-':'';
                $data['transactions'][] = array(
                        'Completed Time' => $transaction['date_added'],
                        'Transaction ID' => $transaction['transaction_id'],
                        'Amount'        => $symbol . '' . $transaction['amount'],
                        'To'            => $transaction['firstname']
                );
        }

        return response()->json($data);
    }

    public function reloadHistory(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        //Get Wallet ID with given Customer ID
        $wallet_table = DB::table('oc_wallet')
                        ->select('*')
                        ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                        ->where('oc_customer.customer_id', $customer_id)
                        ->first();

        $walletID = $wallet_table->wallet_id;

        $reloads = DB::table('oc_bid_transaction')
                        ->select('oc_bid_transaction.date_added','amount','transaction_id')
                        ->leftJoin('oc_transaction_type','oc_bid_transaction.trans_type','=','oc_transaction_type.trans_type_id')
                        ->leftJoin('oc_transaction_status','oc_bid_transaction.status','=','oc_transaction_status.status_id')
                        ->where('oc_bid_transaction.wallet_id',$walletID)
                        ->where('oc_bid_transaction.trans_type', 6)
                        ->where('oc_bid_transaction.status',3)
                        ->orderBy('oc_bid_transaction.date_added','desc')
                        ->simplePaginate($request->limit);

        $reloads->transform(function($i) {
                return (array)$i;
        });

        $array = $reloads->toArray();

        if (!$array){

                return response()->json('Transaction Not Found');

        }

        foreach ($reloads as $reload) {
                $data['transactions'][] = array(
                        'Completed Time' => $reload['date_added'],
                        'Transaction ID' => $reload['transaction_id'],
                        'Amount'         => $reload['amount']
                );
        }

        return response()->json($data);
    }

    public function withdrawHistory(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $data = array();

        $withdraws = DB::table('oc_bid_transaction')
            ->select(
                'oc_bid_transaction.date_added',
                'amount',
                'transaction_id',
                'bank_name',
                'bank_account_number',
                'trans_direction'
            )
            ->leftJoin('oc_transaction_type', 'oc_bid_transaction.trans_type', '=', 'oc_transaction_type.trans_type_id')
            ->leftJoin('oc_transaction_status', 'oc_bid_transaction.status', '=', 'oc_transaction_status.status_id')
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->where('oc_bid_transaction.trans_type', 8)
            ->where('oc_bid_transaction.status', 2)
            ->orderBy('oc_bid_transaction.date_added', 'desc')
            ->simplePaginate($request->limit);

        $withdraws->transform(function ($i) {
            return (array)$i;
        });

        $array = $withdraws->toArray();

        if (!$array) {
            return response()->json('Transaction Not Found');
        }

        foreach ($withdraws as $withdraw) {
            $symbol = $withdraw['trans_direction'] == 'deb' ? '-' : '';
            $data['transactions'][] = array(
                'Completed Time' => $withdraw['date_added'],
                'Transaction ID' => $withdraw['transaction_id'],
                'Amount' => $symbol . ' RM ' . $withdraw['amount'],
                'Bank Name' => $withdraw['bank_name'],
                'Bank Account Number' => $withdraw['bank_account_number']
            );
        }


        return response()->json($data);
    }

    public function referral_earnings()
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $referrals = DB::table('oc_track_referral')
                        ->select('oc_bid_transaction.date_added', 'oc_bid_transaction.transaction_id', 'oc_bid_transaction.amount', 'oc_customer.firstname', 'oc_bid_transaction.amount')
                        ->leftJoin('oc_customer','oc_track_referral.customer_id','=','oc_customer.customer_id')
                        ->leftJoin('oc_wallet','oc_wallet.customer_id','=','oc_track_referral.referrer_id')
                        ->leftJoin('oc_bid_transaction','oc_bid_transaction.wallet_id','=','oc_wallet.wallet_id')
                        ->where('oc_track_referral.referrer_id',$customer_id)
                        ->where('oc_bid_transaction.trans_type', 19)
                        ->where('oc_bid_transaction.status',3)
                        ->get();

        // dd($referrals);

        $referrals->transform(function($i) {
                return (array)$i;
        });

        $array = $referrals->toArray();

        if (!$array){

                return response()->json(['message' => 'Transaction Not Found',
                                        'status'=>'failed'
                                        ], 401);

        }

        foreach ($referrals as $referral) {
                $data['referrals'][] = array(
                        'Completed Time' => $referral['date_added'],
                        'Transaction ID' => $referral['transaction_id'],
                        'Amount' => $referral['amount'],
                        'From' => $referral['firstname']
                );
        }

        return response()->json($data);
    }

    public function eCommerceTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;
        $data = array();

        // $livestores = DB::table('oc_order_cart_list')
        //     ->select(
        //         'oc_bid_transaction.*',
        //         'oc_order.date_added',
        //         'oc_order.order_id',
        //         'oc_product_description.*',
        //         'oc_order_cart_list.*',
        //         'oc_customer.firstname',
        //         'oc_customer.lastname'
        //     )
        //     ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
        //     ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
        //     ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
        //     ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
        //     ->where('oc_order_cart_list.order_status', '=', 2)
        //     ->where('oc_customer.user_id', $user_id)
        //     ->where('oc_bid_transaction.trans_direction', 'cre')
        //     ->where('oc_order_cart_list.type', 'e-commerce')
        //     ->groupBy('oc_order_cart_list.id')
        //     ->simplePaginate($request->limit);

             $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'e-commerce')
            ->simplePaginate($request->limit);

        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['lastname'] . ' ' . $livestore['firstname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity'],
                    'type' => 'e-commerce'
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => 'RM ' . $livestore['shipping'],
                    'Quantity' => $livestore['quantity'],
                    'type' => 'e-commerce'
                );
            }
        }

        return response()->json([
            'data' => $data,
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function auctionLowTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $data = array();

        //Auction Low
        // $livestores = DB::table('oc_order_cart_list')
        //     ->select(
        //         'oc_bid_transaction.*',
        //         'oc_order.date_added',
        //         'oc_order.order_id',
        //         'oc_product_description.*',
        //         'oc_order_cart_list.*',
        //         'oc_customer.firstname',
        //         'oc_customer.lastname'
        //     )
        //     ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
        //     ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
        //     ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
        //     ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
        //     ->where('oc_order_cart_list.order_status', '=', 2)
        //     ->where('oc_customer.user_id', $user_id)
        //     ->where('oc_bid_transaction.trans_direction', 'cre')
        //     ->where('oc_order_cart_list.type', 'auction-low')
        //     ->groupBy('oc_order_cart_list.id')
        //     ->simplePaginate($request->limit);

            $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'auction-low')
            ->simplePaginate($request->limit);

        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity'],
                    'type' => 'auction_low'
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => 'RM ' . $livestore['shipping'],
                    'Quantity' => $livestore['quantity'],
                    'type' => 'auction_low'
                );
            }
        }

        return response()->json([
            'result' => $data,
            'status' => 'success'
        ], 200);
    }

    public function auctionHighTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $data = array();

        $customer_id = $user->customer_id;

        //Auction High
        $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'auction-high')
            ->simplePaginate($request->limit);

        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity'],
                    'type' => 'auction_high'
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity'],
                    'type' => 'auction_high'
                );
            }
        }

        return response()->json([
            'result' => $data,
            'status' => 'success'
        ], 200);
    }

    public function bidFastTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $user_id = $user->user_id;

        //Bid Fast
        $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'bid-fast')
            ->simplePaginate($request->limit);

        // dd($livestores);

        if (!$livestores->count()) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => 'x' . $livestore['quantity'],
                    'type' => 'bid_fast'
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => 'x' . $livestore['quantity'],
                    'type' => 'bid_fast'
                );
            }
        }

        return response()->json($data);
    }

    public function outBidTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $user_id = $user->user_id;
        $data = array();

        //Out Bid
        $livestores = DB::table('oc_order')
            ->select('oc_order.*', 'oc_bid_transaction.*', 'oc_order_product.*')
            ->leftJoin('oc_order_status', 'oc_order.order_status_id', '=', 'oc_order_status.order_status_id')
            ->leftJoin('oc_order_product', 'oc_order_product.order_id', '=', 'oc_order.order_id')
            ->leftJoin('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order.order_id')
            ->leftJoin('oc_customer', 'oc_customer.customer_id', '=', 'oc_order.customer_id')
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_bid_transaction.trans_type', 20)
            ->where('oc_bid_transaction.status', 3)
            ->where('oc_order_status.language_id', 1)
            ->orderBy('oc_order.date_added', 'desc')
            ->simplePaginate($request->limit);

        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity'],
                    'type' => 'out_bid'
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity'],
                    'type' => 'out_bid'
                );
            }
        }

        return response()->json($data);
    }

    public function auctionHigh_LiveTransaction(Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $data = array();

        $user_id = $user->user_id;


            $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'live_auction_high')
            ->simplePaginate($request->limit);



        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['title'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity']
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['title'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity']
                );
            }
        }

        return response()->json($data);
    }

    public function auctionLow_LiveTransaction( Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $data = array();

        $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'live_auction_low')
            ->simplePaginate($request->limit);


        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['title'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity']
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['title'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity']
                );
            }
        }

        return response()->json($data);
    }

    public function eCommerce_LiveTransaction( Request $request)
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $data = array();

        $livestores = DB::table('oc_order_cart_list')
            ->select(
                'oc_bid_transaction.*',
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_product_description.*',
                'oc_order_cart_list.*',
                'oc_customer.firstname',
                'oc_customer.lastname'
            )
            ->join('oc_order', 'oc_order.order_id', '=', 'oc_order_cart_list.order_id')
            ->join('oc_customer', 'oc_customer.user_id', 'oc_order_cart_list.customer_id')
            ->join('oc_product_description', 'oc_product_description.product_id', '=', 'oc_order_cart_list.product_id')
            ->join('oc_bid_transaction', 'oc_bid_transaction.order_id', '=', 'oc_order_cart_list.order_id')
            ->where('oc_order_cart_list.order_status', '=', 2)
            ->where('oc_customer.user_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_order_cart_list.type', 'live_e_commerce')
            ->simplePaginate($request->limit);


        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['username'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity']
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['username'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity']
                );
            }
        }

        return response()->json($data);
    }

    public function bidFast_LiveTransaction()
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $user_id = $user->user_id;

        $livestores = DB::table('oc_order')
            ->select(
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_bid_transaction.transaction_id',
                'oc_customer.*',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_order_product.name',    
                'oc_bid_transaction.amount',
                'oc_order_product.shipping',
                'oc_order_product.quantity'
            )
            ->leftJoin('oc_order_status', 'oc_order.order_status_id', '=', 'oc_order_status.order_status_id')
            ->leftJoin('oc_order_product', 'oc_order_product.order_id', '=', 'oc_order.order_id')
            ->leftJoin(
                'oc_product_description',
                'oc_product_description.product_id',
                '=',
                'oc_order_product.product_id'
            )
            ->leftJoin('oc_product_session', 'oc_product_session.product_id', '=', 'oc_order_product.product_id')
            ->leftJoin(
                'oc_bid_transaction',
                'oc_bid_transaction.product_session_id',
                '=',
                'oc_product_session.session_id'
            )
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->where('oc_bid_transaction.trans_direction', 'cre')
            ->where('oc_bid_transaction.trans_type', 15)
            ->where('oc_order_status.language_id', 1)
            ->groupBy(
                'oc_order.date_added',
                'oc_order.order_id',
                'oc_bid_transaction.transaction_id',
                'oc_customer.*',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_order_product.name',
                'oc_bid_transaction.amount',
                'oc_order_product.shipping',
                'oc_order_product.quantity'
            )
            ->orderBy('oc_order.order_id', 'desc')
            ->get();


        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            if ($livestore['shipping'] == 0) {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['username'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'],
                    'Shipping Fee' => '-',
                    'Quantity' => $livestore['quantity']
                );
            } else {
                $data['livestore'][] = array(
                    'Order Time' => $livestore['date_added'],
                    'Order ID' => $livestore['order_id'],
                    'Transaction ID' => $livestore['transaction_id'],
                    'Stream Name' => $livestore['username'],
                    'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                    'Product Name' => $livestore['name'],
                    'Amount' => 'RM ' . $livestore['amount'] + $livestore['shipping'],
                    'Shipping Fee' => $livestore['shipping'],
                    'Quantity' => $livestore['quantity']
                );
            }
        }

        return response()->json($data);
    }

    public function return_bid_deposits()
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $livestores = DB::table('oc_bid_transaction')
            ->select(
                'oc_streams.title',
                'oc_bid_transaction.date_added',
                'oc_bid_transaction.transaction_id',
                'oc_order_product.name',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_bid_transaction.amount'
            )
            ->leftJoin('oc_customer', 'oc_customer.user_id', '=', 'oc_bid_transaction.user_unique_id')
            ->leftJoin('oc_order', 'oc_order.customer_id', '=', 'oc_customer.customer_id')
            ->rightJoin('oc_order_product', 'oc_order_product.order_id', '=', 'oc_order.order_id')
            ->leftJoin('oc_product_session', 'oc_product_session.session_id', '=', 'oc_order.stream_session_id')
            ->leftJoin('oc_streams', 'oc_streams.id', '=', 'oc_product_session.stream_id')
            ->where('oc_bid_transaction.trans_type', 10)
            ->where('oc_bid_transaction.trans_direction', 'deb')
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->groupBy(
                'oc_streams.title',
                'oc_bid_transaction.date_added',
                'oc_bid_transaction.transaction_id',
                'oc_order_product.name',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_bid_transaction.amount'
            )
            ->get();


        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            $data['livestore'][] = array(
                'Order Time' => $livestore['date_added'],
                'Transaction ID' => $livestore['transaction_id'],
                'Stream Name' => $livestore['title'],
                'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                'Product Name' => $livestore['name'],
                'Amount' => $livestore['amount'],
            );
        }

        return response()->json($data);
    }

    public function forfeit_rate()
    {
        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $livestores = DB::table('oc_bid_transaction')
            ->select(
                'oc_streams.title',
                'oc_bid_transaction.date_added',
                'oc_bid_transaction.transaction_id',
                'oc_order_product.name',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_bid_transaction.amount'
            )
            ->leftJoin('oc_customer', 'oc_customer.user_id', '=', 'oc_bid_transaction.user_unique_id')
            ->leftJoin('oc_order', 'oc_order.customer_id', '=', 'oc_customer.customer_id')
            ->rightJoin('oc_order_product', 'oc_order_product.order_id', '=', 'oc_order.order_id')
            ->leftJoin('oc_product_session', 'oc_product_session.session_id', '=', 'oc_order.stream_session_id')
            ->leftJoin('oc_streams', 'oc_streams.id', '=', 'oc_product_session.stream_id')
            ->where('oc_bid_transaction.trans_type', 18)
            ->where('oc_bid_transaction.trans_direction', 'deb')
            ->where('oc_bid_transaction.user_unique_id', $user_id)
            ->groupBy(
                'oc_streams.title',
                'oc_bid_transaction.date_added',
                'oc_bid_transaction.transaction_id',
                'oc_order_product.name',
                'oc_order.firstname',
                'oc_order.lastname',
                'oc_bid_transaction.amount'
            )
            ->get();


        $livestores->transform(function ($i) {
            return (array)$i;
        });

        $array = $livestores->toArray();

        if (!$array) {
            return response()->json([
                'message' => 'Transaction Not Found',
                'status' => 'failed'
            ], 401);
        }

        foreach ($livestores as $livestore) {
            $data['livestore'][] = array(
                'Order Time' => $livestore['date_added'],
                'Transaction ID' => $livestore['transaction_id'],
                'Stream Name' => $livestore['title'],
                'Customer Name' => $livestore['firstname'] . ' ' . $livestore['lastname'],
                'Product Name' => $livestore['name'],
                'Amount' => 'RM ' . $livestore['amount'],
            );
        }

        return response()->json($data);
    }
}

