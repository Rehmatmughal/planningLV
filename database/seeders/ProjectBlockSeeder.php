<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // project seeder
        $project1 = Project::create(['project_name' => 'Gulberg Greens']);
        $project2 = Project::create(['project_name' => 'Gulberg Residencia']);

        // block seeder
        $blockA = Block::create(['project_id' => $project1->id, 'block_name' => 'A']);
        $blockB = Block::create(['project_id' => $project1->id, 'block_name' => 'B']);
        $blockC = Block::create(['project_id' => $project1->id, 'block_name' => 'C']);
        $blockE = Block::create(['project_id' => $project1->id, 'block_name' => 'E']);
        $blockF = Block::create(['project_id' => $project1->id, 'block_name' => 'F']);
        $blockG = Block::create(['project_id' => $project1->id, 'block_name' => 'G']);
        $blockH = Block::create(['project_id' => $project1->id, 'block_name' => 'H']);
        $blockI = Block::create(['project_id' => $project1->id, 'block_name' => 'I']);
        $blockJ = Block::create(['project_id' => $project1->id, 'block_name' => 'J']);
        $blockK = Block::create(['project_id' => $project1->id, 'block_name' => 'K']);
        $blockL = Block::create(['project_id' => $project1->id, 'block_name' => 'L']);
        $blockM = Block::create(['project_id' => $project1->id, 'block_name' => 'M']);
        $blockN = Block::create(['project_id' => $project1->id, 'block_name' => 'N']);
        $blockO = Block::create(['project_id' => $project1->id, 'block_name' => 'O']);
        $blockP = Block::create(['project_id' => $project1->id, 'block_name' => 'P']);
        $blockQ = Block::create(['project_id' => $project1->id, 'block_name' => 'Q']);
        $blockR = Block::create(['project_id' => $project1->id, 'block_name' => 'R']);
        $blockS = Block::create(['project_id' => $project1->id, 'block_name' => 'S']);
        $blockT = Block::create(['project_id' => $project1->id, 'block_name' => 'T']);
        $blockV = Block::create(['project_id' => $project1->id, 'block_name' => 'V']);
        $blockAA = Block::create(['project_id' => $project1->id, 'block_name' => 'AA']);
        $blockAExecutiveI = Block::create(['project_id' => $project1->id, 'block_name' => 'A-Executive-I']);
        $blockAExecutiveII = Block::create(['project_id' => $project1->id, 'block_name' => 'A-Executive-II']);
        $blockAExecutiveIII = Block::create(['project_id' => $project1->id, 'block_name' => 'A-Executive-III']);
        $blockAExecutiveIV = Block::create(['project_id' => $project1->id, 'block_name' => 'A-Executive-IV']);
        $blockAExecutivePremium = Block::create(['project_id' => $project1->id, 'block_name' => 'A-Executive-Premium']);
        $blockFExecutiveI = Block::create(['project_id' => $project1->id, 'block_name' => 'F-Executive-I']);
        $blockFExecutiveII = Block::create(['project_id' => $project1->id, 'block_name' => 'F-Executive-II']);
        $blockFExecutiveIII = Block::create(['project_id' => $project1->id, 'block_name' => 'F-Executive-III']);
        $blockFExecutiveIV = Block::create(['project_id' => $project1->id, 'block_name' => 'F-Executive-IV']);
        $blockEExecutive = Block::create(['project_id' => $project1->id, 'block_name' => 'E-Executive']);
        $blockBExtensionI = Block::create(['project_id' => $project1->id, 'block_name' => 'B-Extension-I']);
        $blockBExtensionII = Block::create(['project_id' => $project1->id, 'block_name' => 'B-Extension-II']);


    }
}
