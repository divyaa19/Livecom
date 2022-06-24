<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Courier::truncate();

        $Courier =  [
            [
                'shipping_courier_code'=> "abx",
                'shipping_courier_name'=> "ABX Express"
            ],
            [
                'shipping_courier_code'=> "aramex",
                'shipping_courier_name'=> "Aramex"
            ],
            [
                'shipping_courier_code'=> "best",
                'shipping_courier_name'=> "BEST Express"
            ],
            [
                'shipping_courier_code'=> "citylink",
                'shipping_courier_name'=> "City-Link Express"
            ],
            [
                'shipping_courier_code'=> "dhl",
                'shipping_courier_name'=> "DHL Express"
            ],
            [
                'shipping_courier_code'=> "fedex",
                'shipping_courier_name'=> "FedEx Express"
            ],
            [
                'shipping_courier_code'=> "flash",
                'shipping_courier_name'=> "Flash Express"
            ],
            [
                'shipping_courier_code'=> "GDEX",
                'shipping_courier_name'=> "GD Express"
            ],
            [
                'shipping_courier_code'=> "JNT",
                'shipping_courier_name'=> "J&T Express"
            ],
            [
                'shipping_courier_code'=> "ninjavan",
                'shipping_courier_name'=> "Ninja Van"
            ],
            [
                'shipping_courier_code'=> "pgeon",
                'shipping_courier_name'=> "Pgeon"
            ],
            [
                'shipping_courier_code'=> "poslaju",
                'shipping_courier_name'=> "Pos Laju"
            ],
            [
                'shipping_courier_code'=> "posstore",
                'shipping_courier_name'=> "Posstore"
            ],
            [
                'shipping_courier_code'=> "skynet",
                'shipping_courier_name'=> "Sky Net"
            ],
            [
                'shipping_courier_code'=> "zto",
                'shipping_courier_name'=> "ZTO Express"
            ]
          
          ];

          Courier::insert($Courier);
    }
}
