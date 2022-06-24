<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function getAllBank(Request $request): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'success' => true,
                'city' =>
                    DB::table('oc_bank_list')
                        ->select('id', 'bank_name')
                        ->get()
            ]
        );
    }

    /**
     * @param Request $request
     * @param int $state_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBankById(Request $request, int $bank_id): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'success' => true,
                'city' =>
                    DB::table('oc_bank_list')
                        ->select('id', 'bank_name')
                        ->where('id', $bank_id)
                        ->get()
            ]
        );
    }
}
