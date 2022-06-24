<?php

namespace Database\Seeders;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();

        $City = [
            [
                'state_id' => 1,
                'city' => "Batu Pahat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Johor Bahru",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Kluang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Kota Tinggi",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Kulai",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Mersing",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Muar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Pontian",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Segamat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 1,
                'city' => "Tangkak",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Baling",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Bandar Baharu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Alor Setar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Sungai Petani",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Kubang Pasu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Kulim",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Langkawi",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Padang Terap",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Pendang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Pokok Sena",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Sik",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 2,
                'city' => "Yan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Bachok",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Gua Musang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Jeli",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Kota Bharu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Kuala Krai",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Machang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Pasir Mas",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Pasir Puteh",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Tanah Merah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 3,
                'city' => "Tumpat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 4,
                'city' => "Alor Gajah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 4,
                'city' => "Jasin",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 4,
                'city' => "Melaka Tengah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 4,
                'city' => "Masjid Tanah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Jelebu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Jempol",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Kuala Pilah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Teluk Kemang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Rembau",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Seremban",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Tampin",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 5,
                'city' => "Rasah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Bentong",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Bera",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Cameron Higlands",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Jerantut",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Kuantan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Lipis",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Maran",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Pekan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Raub",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Rompin",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 6,
                'city' => "Temerloh",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Kinta",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Larut",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Manjung",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Hilir Perak",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Kerian",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Batang Padang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Kuala Kangsar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Perak Tengah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Hulu Perak",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Kampar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Mualim",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 8,
                'city' => "Bagan Datuk",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 9,
                'city' => "Arau",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 9,
                'city' => "Kangar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 9,
                'city' => "Padang Besar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 7,
                'city' => "Barat Daya",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 7,
                'city' => "Seberang Perai Selatan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 7,
                'city' => "Seberang Perai Tengah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 7,
                'city' => "Seberang Perai Utara",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 7,
                'city' => "Timur Laut",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kota Belud",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kota Kinabalu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Papar",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Penampang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Putatan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Ranau",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Tuaran",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Beaufort",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Nabawan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Keningau",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kuala Penyu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Sepitang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Tambunan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Tenom",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kota Marudu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kudat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Pitas",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Beluran",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kinabatangan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Sandakan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Telupid",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Tongod",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kalabakan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Kunak",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Lahad Datu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Semporna",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 10,
                'city' => "Tawau",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Kuching",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Samarahan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Serian",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Sri Aman",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Betong",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Sarikei",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Mukah",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Sibu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Kapit",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Bintulu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Miri",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 11,
                'city' => "Limbang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Petaling",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Hulu Langat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Klang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Gombak",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Kuala Langat",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Sepang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Kuala Selangor",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Hulu Selangor",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 12,
                'city' => "Sabak Bernam",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Kuala Terengganu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Kemaman",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Dungun",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Besut",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Marang",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Hulu Terengganu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Setiu",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 13,
                'city' => "Kuala Nerus",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 14,
                'city' => "Kuala Lumpur",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 15,
                'city' => "Labuan",
                'created_at' => Carbon::now()
            ],
            [
                'state_id' => 16,
                'city' => "Putrajaya",
                'created_at' => Carbon::now()
            ],
            
        ];

        City::insert($City);
    }
}
