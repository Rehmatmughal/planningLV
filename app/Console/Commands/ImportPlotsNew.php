<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use App\Models\Plotsize;
use App\Models\PropertyType;
use App\Models\PlotSizeAssignment;
use App\Models\PlotCategoryType;
use App\Models\LopStatus;
use App\Models\MortgageStatus;
use App\Models\PlotCoordinate;
use App\Models\DevelopmentStatus;
use App\Models\PropertyTypeAssignment;


class ImportPlotsNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:plots-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import plots data from new CSV format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // CSV file path
        // $path = storage_path('app/V-1&2_List_of_plots-all-incPropertytype-temp1.csv');
        $path = storage_path('app/V-1&2_List_of_plots-all-incPropertytype.csv');

        // Check CSV file exists
        if (! File::exists($path)) {
            $this->error("CSV file not found: {$path}");
            return Command::FAILURE;
        }

        // Read CSV
        $rows = array_map(
            'str_getcsv',
            file($path)
        );

        // Check CSV is not empty
        if (empty($rows)) {
            $this->error('CSV file is empty.');
            return Command::FAILURE;
        }

        // First row = header
        $header = array_map(
            'trim',
            array_shift($rows)
        );

        // Required CSV columns
        $requiredColumns = [
            'Project',
            'Block',
            'Street',
            'Size',
            'Category',
            'LOP-Status',
            'is_Mortgage',
            'Easting',
            'Northing',
            'Rotation',
            'Plot No',
            'Property Type',
            'numbering_type',
            'UTM_E',
            'UTM_N',
            'Latitude',
            'Longitude',
            'pid_lv',
            'possession_status',
            'asphalt_tst',
            'sewer_manholes',
            'overall_status',
        ];

        // Check required columns
        foreach ($requiredColumns as $column) {
            if (! in_array($column, $header)) {
                $this->error(
                    "Required CSV column missing: {$column}"
                );

                return Command::FAILURE;
            }
        }

        $this->info(
            'CSV loaded successfully. Total rows: ' . count($rows)
        );

        DB::beginTransaction();
// IMPORT LOGIC START
        try {


            foreach ($rows as $row) {

                // Convert CSV row into associative array
                $data = array_combine($header, $row);

                /*
                |--------------------------------------------------------------------------
                | 1. PROJECT
                |--------------------------------------------------------------------------
                */

                $project = Project::firstOrCreate([
                    'project_name' => trim($data['Project']),
                ]);

                /*
                |--------------------------------------------------------------------------
                | 2. BLOCK
                |--------------------------------------------------------------------------
                */

                $block = Block::firstOrCreate([
                    'block_name' => trim($data['Block']),
                    'project_id' => $project->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 3. STREET
                |--------------------------------------------------------------------------
                */

                $street = Street::firstOrCreate([
                    'street_name' => trim($data['Street']),
                    'block_id'    => $block->id,
                    'project_id'  => $project->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 4. PROPERTY TYPE
                |--------------------------------------------------------------------------
                */

                $propertyType = PropertyType::firstOrCreate([
                    'name' => trim($data['Property Type']),
                ]);

                /*
                |--------------------------------------------------------------------------
                | 5. PROPERTY TYPE ASSIGNMENT
                |--------------------------------------------------------------------------
                */

                PropertyTypeAssignment::firstOrCreate([
                    'project_id'       => $project->id,
                    'block_id'         => $block->id,
                    'property_type_id' => $propertyType->id,
                ]);                

                /*
                |--------------------------------------------------------------------------
                | 6. PLOT SIZE MASTER
                |--------------------------------------------------------------------------
                */

                // $size = Plotsize::firstOrCreate([
                //     'title' => trim($data['Size']),
                // ]);
                $size = Plotsize::firstOrCreate([
                    'title'      => trim($data['Size']),
                    'project_id' => $project->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 7. PLOT SIZE ASSIGNMENT
                |--------------------------------------------------------------------------
                */

                PlotSizeAssignment::firstOrCreate([
                    'project_id'      => $project->id,
                    'block_id'        => $block->id,
                    'property_type_id'=> $propertyType->id,
                    'plotsize_id'     => $size->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | 8. PLOT CATEGORY
                |--------------------------------------------------------------------------
                */

                $category = PlotCategoryType::firstOrCreate([
                    'category_title' => trim($data['Category']),
                ]);

                /*
                |--------------------------------------------------------------------------
                | 9. PLOT
                |--------------------------------------------------------------------------
                */

                $plot = Plot::firstOrCreate(
                    [
                        'plot_number'      => trim($data['Plot No']),
                        'project_id'       => $project->id,
                        'block_id'         => $block->id,
                        'street_id'        => $street->id,
                        'property_type_id' => $propertyType->id,
                    ],
                    [
                        'size_id'          => $size->id,
                        'category_id'      => $category->id,
                        'numbering_type'   => strtolower(
                            trim($data['numbering_type'])
                        ),
                        'pid_lv'           => ! empty(trim($data['pid_lv']))
                            ? trim($data['pid_lv'])
                            : null,
                    ]
                );
                
                // $plot = Plot::firstOrCreate(
                //     [
                //         'plot_number' => trim($data['Plot No']),
                //         'project_id'  => $project->id,
                //         'block_id'    => $block->id,
                //         'street_id'   => $street->id,
                //     ],
                //     [
                //         'property_type_id' => $propertyType->id,
                //         'size_id'          => $size->id,
                //         'category_id'      => $category->id,
                //         'numbering_type'   => strtolower(
                //             trim($data['numbering_type'])
                //         ),
                //         'pid_lv'           => ! empty(trim($data['pid_lv']))
                //             ? trim($data['pid_lv'])
                //             : null,
                //     ]
                // );

                /*
                |--------------------------------------------------------------------------
                | 10. LOP STATUS
                |--------------------------------------------------------------------------
                */

                LopStatus::updateOrCreate(
                    [
                        'plot_id' => $plot->id,
                    ],
                    [
                        'lop_status' => strtolower(
                            trim($data['LOP-Status'])
                        ) === 'lop'
                            ? 'lop'
                            : 'non_lop',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | 11. MORTGAGE STATUS
                |--------------------------------------------------------------------------
                */

                MortgageStatus::updateOrCreate(
                    [
                        'plot_id' => $plot->id,
                    ],
                    [
                        'is_mortgaged' => strtolower(
                            trim($data['is_Mortgage'])
                        ) === 'yes'
                            ? 'yes'
                            : 'no',
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | 12. PLOT COORDINATES
                |--------------------------------------------------------------------------
                */

                PlotCoordinate::updateOrCreate(
                    [
                        'plot_id' => $plot->id,
                    ],
                    [
                        'Easting'   => $data['Easting'] ?? 0,
                        'Northing'  => $data['Northing'] ?? 0,
                        'UTM_E'     => $data['UTM_E'] ?? 0,
                        'UTM_N'     => $data['UTM_N'] ?? 0,
                        'latitude'  => $data['Latitude'] ?? 0,
                        'longitude' => $data['Longitude'] ?? 0,
                        'Rotation'  => $data['Rotation'] ?? 0,
                    ]
                );
                
                /*
                |--------------------------------------------------------------------------
                | 13. DEVELOPMENT STATUS
                |--------------------------------------------------------------------------
                */

                DevelopmentStatus::updateOrCreate(
                    [
                        'plot_id' => $plot->id,
                    ],
                    [
                        'sewer_manholes' => strtolower(
                            trim($data['sewer_manholes'])
                        ),

                        'asphalt_tst' => strtolower(
                            trim($data['asphalt_tst'])
                        ),

                        'overall_status' => strtolower(
                            trim($data['overall_status'])
                        ),
                    ]
                );       

            }

            DB::commit();

            $this->info(
                'CSV import completed successfully.'
            );

            return Command::SUCCESS;

        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error(
                'CSV import failed: ' . $e->getMessage()
            );

            return Command::FAILURE;
        }
// m b end
    }
    
}