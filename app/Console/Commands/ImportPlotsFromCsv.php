<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use App\Models\PlotSize;
use App\Models\PlotCategoryType;
use App\Models\LopStatus;
use App\Models\MortgageStatus;
use App\Models\PlotCoordinate;
use App\Models\DevelopmentStatus;

class ImportPlotsFromCsv extends Command
{
    protected $signature = 'import:plots';
    protected $description = 'Import plots data from CSV into database';

    public function handle()
    {
        $path = storage_path('app/V-1&2_List_of_plots-all.csv');
        // $path = storage_path('app/V-2_List_of_plots-temp.csv');
        if (!File::exists($path)) {
            $this->error("CSV file not found");
            return;
        }

        DB::beginTransaction();

        try {

            // /* ===============================
            //  | 1. PROJECT
            //  ===============================*/
            // $project = Project::firstOrCreate([
            //     'project_name' => 'Gulberg Residencia'
            // ]);

            /* ===============================
             | CSV READ
             ===============================*/
            $rows = array_map('str_getcsv', file($path));
            $header = array_map('trim', array_shift($rows));

            foreach ($rows as $row) {

                $data = array_combine($header, $row);

                /* ===============================
                | 1. PROJECT
                ===============================*/
                $project = Project::firstOrCreate([
                    // 'project_name' => 'Gulberg Residencia'
                    'project_name' => $data['Project']
                ]);

                /* ===============================
                 | 2. BLOCK
                 ===============================*/
                $block = Block::firstOrCreate([
                    'block_name' => trim($data['Block']),
                    'project_id' => $project->id
                ]);

                /* ===============================
                 | 3. STREET
                 ===============================*/
                $street = Street::firstOrCreate([
                    'street_name' => trim($data['Street']),
                    'block_id'    => $block->id,
                    'project_id'  => $project->id
                ]);

                /* ===============================
                 | 4. PLOT SIZE
                 ===============================*/
                $size = PlotSize::firstOrCreate([
                    'title'      => trim($data['Size']),
                    'project_id' => $project->id
                ]);

                /* ===============================
                 | 5. CATEGORY
                 ===============================*/
                $category = PlotCategoryType::firstOrCreate([
                    'category_title' => trim($data['Category'])
                ]);

                /* ===============================
                 | 6. PLOT
                 ===============================*/
                $plot = Plot::firstOrCreate([
                    'plot_number' => trim($data['Plot No']),
                    'block_id'    => $block->id,
                    'street_id'   => $street->id,
                    'project_id'  => $project->id
                ], [
                    'size_id'        => $size->id,
                    'category_id'    => $category->id,
                    'numbering_type' => 'blockwise'
                ]);

                /* ===============================
                 | 7. LOP STATUS
                 ===============================*/
                LopStatus::updateOrCreate(
                    ['plot_id' => $plot->id],
                    ['lop_status' => strtolower(trim($data['LOP-Status'])) == 'lop' ? 'lop' : 'non_lop']
                );

                /* ===============================
                 | 8. MORTGAGE STATUS
                 ===============================*/
                MortgageStatus::updateOrCreate(
                    ['plot_id' => $plot->id],
                    ['is_mortgaged' => strtolower(trim($data['is_Mortgage'])) == 'yes' ? 'yes' : 'no']
                );

                /* ===============================
                 | 9. COORDINATES
                 ===============================*/
                PlotCoordinate::updateOrCreate(
                    ['plot_id' => $plot->id],
                    [
                        'Easting'   => $data['Easting'] ?? 0,
                        'Northing'  => $data['Northing'] ?? 0,
                        'UTM_E'  => $data['UTM_E'] ?? 0,
                        'UTM_N'  => $data['UTM_N'] ?? 0,
                        'latitude'  => $data['Latitude'] ?? 0,
                        'longitude' => $data['Longitude'] ?? 0,
                        'Rotation'  => $data['Rotation'] ?? 0,
                    ]
                );

                /* ===============================
                 | 10. DEVELOPMENT DEFAULT
                 ===============================*/
                DevelopmentStatus::firstOrCreate(
                    ['plot_id' => $plot->id],
                    [
                        'sewer_manholes' => $data['sewer_manholes'],
                        'asphalt_tst'    => $data['asphalt_tst'],
                        'overall_status' => $data['overall_status']
                    ]
                );
            }

            DB::commit();
            $this->info('✅ CSV import completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
