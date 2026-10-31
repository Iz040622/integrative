<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/roomType.json'));
        $roomTypes = json_decode($json, true);

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }
    }
}
