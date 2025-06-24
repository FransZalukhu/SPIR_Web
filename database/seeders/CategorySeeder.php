<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Jalan Rusak', 'description' => 'Jalan berlubang atau tidak rata yang membahayakan pengendara'],
            ['name' => 'Lampu Mati', 'description' => 'Lampu jalan yang mati atau rusak'],
            ['name' => 'Saluran Tersumbat', 'description' => 'Got atau drainase mampet'],
            ['name' => 'Sampah Menumpuk', 'description' => 'Tumpukan sampah yang tidak diangkut'],
            ['name' => 'Fasilitas Umum Rusak', 'description' => 'Bangku, taman, atau halte yang rusak'],
            ['name' => 'Pohon Tumbang', 'description' => 'Pohon tumbang yang menutupi jalan atau membahayakan'],
            ['name' => 'Rambu Tidak Jelas', 'description' => 'Rambu atau marka jalan yang tidak terlihat atau hilang'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
