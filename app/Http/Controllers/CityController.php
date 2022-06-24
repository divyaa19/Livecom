<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * @param Request $request
     * @param int $state_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCity(Request $request, int $state_id): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'success' => true,
                'city' =>
                    DB::table('oc_city')
                        ->select('id', 'city', 'state_id')
                        ->where('state_id', $state_id)
                        ->get()
            ]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'success' => true,
                'city' =>
                    DB::table('oc_city')
                        ->select('id', 'city', 'state_id')
                        ->get()
            ]
        );
    }
}
