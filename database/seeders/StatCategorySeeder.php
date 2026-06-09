<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatCategorySeeder extends Seeder
{
  public function run(): void
    {
        $categories = [
            ['name' => 'Ofensivo', 'description' => 'Acciones que generan peligro o gol'],
            ['name' => 'Defensivo', 'description' => 'Acciones que evitan el peligro'],
            ['name' => 'Mediocampo', 'description' => 'Acciones de distribución y construcción del juego'],
        ];

        foreach ($categories as $category) {
            \App\Models\StatCategory::create($category);
        }
    }
}
