<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Villa',
            'Land',
            'Commercial Shop',
            'Residential Apartment',
        ];

        foreach ($types as $type) {
            PropertyType::firstOrCreate(['name' => $type]);
        }
    }
}
