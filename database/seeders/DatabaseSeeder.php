<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // $this->call(PlotSystemSeeder::class);
        // $this->call(RolePermissionSeeder::class);
        // working --- start ---
        $this->call(RolePermissionNSeeder::class); // new seeder copy from other projects
        // $this->call(PlotSystemTempSeeder::class);
        // working --- end ---
        
        // $this->call(CategoryTypeSeeder::class);
        // ProjectBlockSeeder

        // $this->call(ProjectSeeder::class);
        // $this->call(BlocksSeeder::class);
        // $this->call(GrblockSeeder::class);
        // $this->call(GgblockSeeder::class);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
