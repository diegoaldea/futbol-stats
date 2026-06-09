<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            ['name' => 'Gol', 'stat_category_id' => 1, 'points' => 1.00, 'is_global' => true],
            ['name' => 'Asistencia', 'stat_category_id' => 1, 'points' => 0.75, 'is_global' => true],
            ['name' => 'Tiro al arco', 'stat_category_id' => 1, 'points' => 0.25, 'is_global' => true]
        ];
        
        foreach ($stats as $stat) {
            \App\Models\Stat::create($stat);
        }
    }
}
