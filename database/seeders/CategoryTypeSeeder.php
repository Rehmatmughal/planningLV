<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('plot_category_types')->insert([
            [
                'category_title' => 'General',
                'remarks' => 'General Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'category_title' => 'Corner',
                'remarks' => 'Corner Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'category_title' => 'Main Road',
                'remarks' => 'Main Road Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'category_title' => 'Main Road Corner',
                'remarks' => 'Main Road Corner Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'category_title' => 'Parkface',
                'remarks' => 'Parkface Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'category_title' => 'Parkface Corner',
                'remarks' => 'Parkface Corner Plot',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
