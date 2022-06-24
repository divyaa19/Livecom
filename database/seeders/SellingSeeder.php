<?php

namespace Database\Seeders;

use App\Models\SellingMode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SellingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SellingMode::truncate();

        $Selling = [
            [
                'selling_mode' => "LiveStore",
                'description' => "List your product on LiveStore",
                'created_at' => Carbon::now()
            ],
            [
                'selling_mode' => "LiveStream",
                'description' => "Sell your product on LiveStream",
                'created_at' => Carbon::now()
            ],
        ];

        SellingMode::insert($Selling);
    }
}
