<?php

namespace Database\Seeders;

use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::truncate();

        $Region =  [
            [
                'region'=> "Peninsular",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "East Malaysia",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Brunei",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Singapore",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Burma",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Cambodia",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Timor-Leste",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Indonesia",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Laos",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Philippines",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Thailand",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ],
            [
                'region'=> "Vietnam",
                'created_at'=>Carbon::now(),
                'updated_at'=> Carbon::now()
            ]
          ];

          Region::insert($Region);
    }
}
