<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatesController extends Controller
{
    public function getStates(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'success' => true,
            'state' => DB::table('oc_states')
                ->select('id', 'states')
                ->get()
        ]);
    }
}
