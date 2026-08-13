<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('projects')->insert([
            [
                'project_name' => 'Gulberg Residencia',
                'project_remarks' => 'Luxury housing project in islamabad.',
            ],
            [
                'project_name' => 'Gulberg Greens',
                'project_remarks' => 'Luxury form housing project in islamabad.',
            ],
            
        ]);
    }
}
