<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Customer;
class MessageController extends Controller
{
    public function __construct()
    {
        //
    }
	public function sendMessaget(){
		
	}
    public function sendMessage(Request $request){
      event(new \App\Events\ExampleEvent($request->message));
    }
}