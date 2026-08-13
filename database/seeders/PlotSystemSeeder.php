<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use App\Models\PlotCategoryType;
use App\Models\Plotsize;
use Illuminate\Database\Eloquent\Model;

class PlotSystemSeeder extends Seeder
{
    public function run(): void
    {
        // -------- Projects -----------
        $project1 = Project::create(['project_name' => 'Gulberg Greens']);
        $project2 = Project::create(['project_name' => 'Gulberg Residencia']);

        // -------- Blocks (Residencia) -----------
        $blockA = Block::create(['project_id' => $project2->id, 'block_name' => 'A']);
        $blockB = Block::create(['project_id' => $project2->id, 'block_name' => 'B']);
        $blockC = Block::create(['project_id' => $project2->id, 'block_name' => 'C']);
        // (All other blocks ... same as your data)

        // -------- Blocks (Greens) -----------
        $blockGA = Block::create(['project_id' => $project1->id, 'block_name' => 'A']);
        $blockGB = Block::create(['project_id' => $project1->id, 'block_name' => 'B']);
        $blockGC = Block::create(['project_id' => $project1->id, 'block_name' => 'C']);
        $blockGD = Block::create(['project_id' => $project1->id, 'block_name' => 'D']);
        $blockGE = Block::create(['project_id' => $project1->id, 'block_name' => 'E']);

        Model::unsetEventDispatcher();

        // -------- Category Example ----------
        $category01 = PlotCategoryType::create([
            'category_title' => 'General',
        ]);

        $category02 = PlotCategoryType::create([
            'category_title' => 'Corner',
        ]);
        $category03 = PlotCategoryType::create([
            'category_title' => 'Main Road',
        ]);

        $category04 = PlotCategoryType::create([
            'category_title' => 'Main Road Corner',
        ]);

        $category05 = PlotCategoryType::create([
            'category_title' => 'Parkface',
        ]);

        $category06 = PlotCategoryType::create([
            'category_title' => 'Parkface Corner',
        ]);


        // -------- Streets Example (For Block A of Residencia) ----------
        $streetA1 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockA->id,
            'street_name' => 'St - 11',
            'numbering_type' => 'blockwise'
        ]);

        $streetA2 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockA->id,
            'street_name' => 'St - 12',
            'numbering_type' => 'blockwise'
        ]);

        $streetA3 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockA->id,
            'street_name' => 'St - 13',
            'numbering_type' => 'blockwise'
        ]);

        $streetB1 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockB->id,
            'street_name' => 'St - 21',
            'numbering_type' => 'blockwise'
        ]);

        $streetB2 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockB->id,
            'street_name' => 'St - 22',
            'numbering_type' => 'blockwise'
        ]);

        $streetB3 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockB->id,
            'street_name' => 'St - 23',
            'numbering_type' => 'blockwise'
        ]);

        $streetC1 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockC->id,
            'street_name' => 'St - 31',
            'numbering_type' => 'blockwise'
        ]);

        $streetC2 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockC->id,
            'street_name' => 'St - 32',
            'numbering_type' => 'blockwise'
        ]);

        $streetC3 = Street::create([
            'project_id' => $project2->id,
            'block_id'   => $blockC->id,
            'street_name' => 'St - 33',
            'numbering_type' => 'blockwise'
        ]);

        // streets gulberg Greens

        $streetGA1 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGA->id,
            'street_name' => 'St - 01',
            'numbering_type' => 'blockwise'
        ]);

        $streetGA2 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGA->id,
            'street_name' => 'St - 02',
            'numbering_type' => 'blockwise'
        ]);
        
        $streetGA3 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGA->id,
            'street_name' => 'St - 03',
            'numbering_type' => 'blockwise'
        ]);

        $streetGB1 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGB->id,
            'street_name' => 'St - 01',
            'numbering_type' => 'blockwise'
        ]);
                
        $streetGB2 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGB->id,
            'street_name' => 'St - 03',
            'numbering_type' => 'blockwise'
        ]);

        $streetGC1 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGC->id,
            'street_name' => 'St - 04',
            'numbering_type' => 'blockwise'
        ]);

        $streetGC2 = Street::create([
            'project_id' => $project1->id,
            'block_id'   => $blockGC->id,
            'street_name' => 'St - 05',
            'numbering_type' => 'blockwise'
        ]);

        // (Your remaining 20+ streets — keep as-is)

        // -------- Plot Sizes (New System) ----------

        $size_25x50 = Plotsize::create([
            'title' => '25x50',
            'size_area' => 125,
            'project_id' => $project2->id,
        ]);

        $size_30x60 = Plotsize::create([
            'title' => '30x60',
            'size_area' => 200,
            'project_id' => $project2->id,
            'remarks' => null,
        ]);

        $size_35x70 = Plotsize::create([
            'title' => '35x70',
            'size_area' => 272.22,
            'project_id' => $project2->id,
        ]);

        $size_40x80 = Plotsize::create([
            'title' => '40x80',
            'size_area' => 355.55,
            'project_id' => $project2->id,
        ]);

        $size_50x90 = Plotsize::create([
            'title' => '50x90',
            'size_area' => 500,
            'project_id' => $project2->id,
        ]);

        $size_75x120 = Plotsize::create([
            'title' => '75x120',
            'size_area' => 1000,
            'project_id' => $project2->id,
        ]);

        $size_2400 = Plotsize::create([
            'title' => '2400',
            'size_area' => 2400,
            'project_id' => $project1->id,
        ]);

        $size_3000 = Plotsize::create([
            'title' => '3000',
            'size_area' => 3000,
            'project_id' => $project1->id,
        ]);

        $size_6000 = Plotsize::create([
            'title' => '6000',
            'size_area' => 6000,
            'project_id' => $project1->id,
        ]);

        // -------- Plots --------------
        // Block-wise old system
        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockA->id,
            'street_id' => $streetA1->id,
            'plot_number' => '1',
            'size_id' => $size_25x50->id,
            'category_id'   => $category01->id,
            'numbering_type' => 'blockwise',
            'remarks' => 'Blockwise numbering'
        ]);

        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockA->id,
            'street_id' => $streetA1->id,
            'category_id'   => $category02->id,
            'plot_number' => '2',
            'size_id' => $size_35x70->id,
            'numbering_type' => 'blockwise',
            'remarks' => 'Blockwise numbering'
        ]);

        // Streetwise new system
        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockB->id,
            'street_id' => $streetA1->id,
            'category_id'   => $category03->id,
            'plot_number' => '1',
            'size_id' => $size_25x50->id,
            'numbering_type' => 'streetwise',
            'remarks' => 'Blockwise numbering'
        ]);

        Plot::create([
            'project_id' => $project2->id,
            'block_id' => $blockC->id,
            'street_id' => $streetC1->id,
            'category_id'   => $category04->id,
            'plot_number' => '3',
            'size_id' => $size_30x60->id,
            'numbering_type' => 'Blockwise',
        ]);
    }
}
