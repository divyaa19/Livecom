<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function getCourier(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'courier' => DB::table('oc_shipping_courier')
                ->select('*')
                ->get()
        ]);
    }
}