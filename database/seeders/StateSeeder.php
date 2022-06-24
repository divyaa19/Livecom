<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\States;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        States::truncate();

        $State = [
            [
                'id' => 1,
                'region_id' => "1",
                'states' => "Johor",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'region_id' => "1",
                'states' => "Kedah",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 3,
                'region_id' => "1",
                'states' => "Kelantan",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 4,
                'region_id' => "1",
                'states' => "Malacca",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 5,
                'region_id' => "1",
                'states' => "Negeri Sembilan",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 6,
                'region_id' => "1",
                'states' => "Pahang",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 7,
                'region_id' => "1",
                'states' => "Pulau Pinang",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 8,
                'region_id' => "1",
                'states' => "Perak",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 9,
                'region_id' => "1",
                'states' => "Perlis",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 10,
                'region_id' => "2",
                'states' => "Sabah",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 11,
                'region_id' => "2",
                'states' => "Sarawak",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 12,
                'region_id' => "1",
                'states' => "Selangor",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 13,
                'region_id' => "1",
                'states' => "Terengganu",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 14,
                'region_id' => "1",
                'states' => "Kuala Lumpur",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 15,
                'region_id' => "2",
                'states' => "Labuan",
                'created_at' => Carbon::now()
            ],
            [
                'id' => 16,
                'region_id' => "1",
                'states' => "Putrajaya",
                'created_at' => Carbon::now()
            ],
        ];
        States::insert($State);
    }
}
