<?php

namespace Database\Seeders;

use App\Models\LeaveReasonsQuestion;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LeaveReasonsQuestion::truncate();

        $Question = [
            [
                'reasons' => "I'm leaving temporarily",
                'created_at' => Carbon::now()
            ],
            [
                'reasons' => 'Trouble getting started',
                'created_at' => Carbon::now()
            ],
            [
                'reasons' => 'Privacy concerns',
                'created_at' => Carbon::now()
            ],
            [
                'reasons' => "I don't feel safe",
                'created_at' => Carbon::now()
            ],
            [
                'reasons' => "I'm on LiveCom too much",
                'created_at' => Carbon::now()
            ],
            [
                'reasons' => "Too many irrelevant ads",
                'created_at' => Carbon::now()
            ],
        ];

        LeaveReasonsQuestion::insert($Question);
    }
}
