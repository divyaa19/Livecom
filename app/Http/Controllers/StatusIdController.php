<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatusIdController extends Controller
{
    public function getstatus()
    {
        $status_name=DB::table('oc_order_status')->get();
        return response()->json([$status_name],200);
    }
    public function getstage()
    {
        $status_stage=DB::table('oc_order_stage')->get();
        return response()->json([$status_stage],200);
    }
    public function getcountry()
    {
        $status_country=DB::table('oc_country')->get();
        return response()->json([$status_country],200);
    }
    public function getzone()
    {
        $status_zone=DB::table('oc_zone')->get();
        return response()->json([$status_zone],200);
    }

    public function getcourierid()
    {
        $status_courier=DB::table('oc_shipping_courier')->get();
        return response()->json([$status_courier],200);
    }
}