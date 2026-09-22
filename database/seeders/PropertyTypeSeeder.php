<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Residencia Plot',
            'Greens Plot',
            'Commercial Plot',
            'Apartment Site',
            'School',
            'Mosque',
            'Public Building',
        ];

        foreach ($types as $type) {
            PropertyType::firstOrCreate([
                'name' => $type,
            ]);
        }
    }
}