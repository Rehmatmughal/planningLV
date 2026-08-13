<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Block;
use App\Models\LopStatus;
use App\Models\Street;
use App\Models\plotsize;
use App\Models\Plot;
use App\Models\PlotCategoryType;
use Illuminate\Support\Facades\DB;



class PlotSystemTempSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------- Projects -----------
        $project1 = Project::create(['project_name' => 'Gulberg Greens']);
        $project2 = Project::create(['project_name' => 'Gulberg Residencia']);

        // -------- plotsizes -----------
        
       
        // DB::table('plotsizes')->insert([
        //     [
        //         'plotsize' => '25x50',
        //         'sizearea' => 138.88,
        //         'project_id' => $project2->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '30x60',
        //         'sizearea' => 200,
        //         'project_id' => $project2->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '35x70',
        //         'sizearea' => 272.22,
        //         'project_id' => $project2->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '40x80',
        //         'sizearea' => 355.55,
        //         'project_id' => $project2->id,
        //         'remarks' => null,  
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '50x90',
        //         'sizearea' => 500,
        //         'project_id' => $project2->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '75x120',
        //         'sizearea' => 1000,
        //         'project_id' => $project2->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '4K',
        //         'sizearea' => 2400,
        //         'project_id' => $project1->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '5K',
        //         'sizearea' => 3000,
        //         'project_id' => $project1->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],[
        //         'plotsize' => '10K',
        //         'sizearea' => 6000,
        //         'project_id' => $project1->id,
        //         'remarks' => null,
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ],
        //     ]);

        $sizeR1 = plotsize::create(['title' => '25x50', 'size_area' => 138.88, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeR2 = plotsize::create(['title' => '30x60', 'size_area' => 200.00, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeR3 = plotsize::create(['title' => '35x70', 'size_area' => 272.22, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeR4 = plotsize::create(['title' => '40x80', 'size_area' => 355.55, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeR5 = plotsize::create(['title' => '50x90', 'size_area' => 500.00, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeR6 = plotsize::create(['title' => '75x120', 'size_area' => 1000, 'project_id' => $project2->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeG1 = plotsize::create(['title' => '4K', 'size_area' => 2400, 'project_id' => $project1->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeG2 = plotsize::create(['title' => '5K', 'size_area' => 3000, 'project_id' => $project1->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);
        $sizeG3 = plotsize::create(['title' => '10K', 'size_area' => 6000, 'project_id' => $project1->id, 'remarks' => null,  'created_at' => now(), 'updated_at' => now()]);

         // -------- Blocks -----------
      
        $blockA = Block::create(['project_id' => $project2->id, 'block_name' => 'A']);
        $blockB = Block::create(['project_id' => $project2->id, 'block_name' => 'B']);
        $blockC = Block::create(['project_id' => $project2->id, 'block_name' => 'C']);
        $blockE = Block::create(['project_id' => $project2->id, 'block_name' => 'E']);
        $blockF = Block::create(['project_id' => $project2->id, 'block_name' => 'F']);
        // Greens
        $blockGA = Block::create(['project_id' => $project1->id, 'block_name' => 'A']);
        $blockGB = Block::create(['project_id' => $project1->id, 'block_name' => 'B']);
        $blockGC = Block::create(['project_id' => $project1->id, 'block_name' => 'C']);
        $blockGD = Block::create(['project_id' => $project1->id, 'block_name' => 'D']);
        $blockGE = Block::create(['project_id' => $project1->id, 'block_name' => 'E']);
        $blockGResidencialCompundA = Block::create(['project_id' => $project2->id, 'block_name' => 'Residencial-Compund-A']);

        // -------- Streets -----------
        $streetRA1 = Street::create(['project_id' => $project2->id, 'block_id' => $blockA->id, 'street_name' => 'St-01']);
        $streetRA2 = Street::create(['project_id' => $project2->id, 'block_id' => $blockA->id, 'street_name' => 'St-02']);
        $streetRB1 = Street::create(['project_id' => $project2->id, 'block_id' => $blockB->id, 'street_name' => 'St-01']);
        $streetRB2 = Street::create(['project_id' => $project2->id, 'block_id' => $blockB->id, 'street_name' => 'St-02']);
        $streetRC1 = Street::create(['project_id' => $project2->id, 'block_id' => $blockC->id, 'street_name' => 'St-01']);
        $streetRC2 = Street::create(['project_id' => $project2->id, 'block_id' => $blockC->id, 'street_name' => 'St-02']);
        $streetRE1 = Street::create(['project_id' => $project2->id, 'block_id' => $blockE->id, 'street_name' => 'St-01']);
        $streetRF1 = Street::create(['project_id' => $project2->id, 'block_id' => $blockF->id, 'street_name' => 'St-02']);

        $category01 = PlotCategoryType::Create(['category_title' => 'General', 'remarks' => 'General Plot', 'created_at' => now(), 'updated_at' => now()]);
        $category02 = PlotCategoryType::Create(['category_title' => 'Corner', 'remarks' => 'Corner Plot', 'created_at' => now(), 'updated_at' => now()]);
        $category03 = PlotCategoryType::Create(['category_title' => 'Main Road', 'remarks' => 'Main Road Plot', 'created_at' => now(), 'updated_at' => now()]);
        $category04 = PlotCategoryType::Create(['category_title' => 'Main Road Corner', 'remarks' => 'Main Road Corner Plot', 'created_at' => now(), 'updated_at' => now()]);
        $category05 = PlotCategoryType::Create(['category_title' => 'Parkface', 'remarks' => 'Parkface Plot', 'created_at' => now(), 'updated_at' => now()]);
        $category06 = PlotCategoryType::Create(['category_title' => 'Parkface Corner', 'remarks' => 'Parkface Corner Plot', 'created_at' => now(), 'updated_at' => now()]);
        
        // -------- Plots ------------
        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockA->id,
            'street_id' => $streetRA1->id,
            'plot_number' => '1',
            'size_id' => $sizeR1->id,
            'category_id'   => $category01->id,
            'numbering_type' => 'blockwise',
            'remarks' => 'Blockwise numbering'
        ]);

        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockA->id,
            'street_id' => $streetRA2->id,
            'category_id'   => $category02->id,
            'plot_number' => '2',
            'size_id' => $sizeR2->id,
            'numbering_type' => 'blockwise',
            'remarks' => 'Blockwise numbering'
        ]);

        // Streetwise new system
        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockB->id,
            'street_id' => $streetRB1->id,
            'category_id'   => $category03->id,
            'plot_number' => '1',
            'size_id' => $sizeR3->id,
            'numbering_type' => 'streetwise',
            'remarks' => 'Blockwise numbering'
        ]);

        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockC->id,
            'street_id' => $streetRE1->id,
            'category_id'   => $category04->id,
            'plot_number' => '3',
            'size_id' => $sizeR4->id,
            'numbering_type' => 'Blockwise',
        ]);

    }
}
