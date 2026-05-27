<?php

namespace Database\Seeders;

use App\Models\MaterialType;
use Illuminate\Database\Seeder;

class MaterialTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            '20 MM (Kapchi)',
            '10 MM (Grit)',
            '6 MM',
            '40 MM',
            '60 MM',
            '90 MM',
            'GSB',
            'W.M.M.',
            'Ruble',
            '0 MM (Dust)',
        ];

        foreach ($materials as $material) {
            MaterialType::firstOrCreate(['name' => $material]);
        }
    }
}
