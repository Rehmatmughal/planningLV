<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use App\Models\Project;
use App\Models\Block;
use App\Models\Plot;
use App\Models\AreaVariation;

class ImportAreaVariations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:area-variations';

    /**
     * The console command description.
     */
    protected $description = 'Import Area Variations data from CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Unlimited execution time
        set_time_limit(0);

        // Unlimited memory
        ini_set('memory_limit', '-1');

        /*
        |--------------------------------------------------------------------------
        | CSV FILE
        |--------------------------------------------------------------------------
        */

        $path = storage_path('app/Area-variations.csv');

        if (! File::exists($path)) {

            $this->error(
                "CSV file not found: {$path}"
            );

            return Command::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | READ CSV
        |--------------------------------------------------------------------------
        */

        $rows = array_map(
            'str_getcsv',
            file($path)
        );

        if (empty($rows)) {

            $this->error(
                'CSV file is empty.'
            );

            return Command::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        $header = array_map(
            'trim',
            array_shift($rows)
        );


        /*
        |--------------------------------------------------------------------------
        | REQUIRED COLUMNS
        |--------------------------------------------------------------------------
        */

        $requiredColumns = [
            'Project',
            'Block',
            'Plot',
            'measured_area',
            'possession_status',
            'remarks',
            'source',
            'workflow_status',
            'created_at',
            'LOP_status',
            'asphalttstroad_status',
            'sewer_status',
        ];


        foreach ($requiredColumns as $column) {

            if (! in_array($column, $header)) {

                $this->error(
                    "Required CSV column missing: {$column}"
                );

                return Command::FAILURE;
            }
        }


        $this->info(
            'CSV loaded successfully. Total rows: ' .
            count($rows)
        );


        /*
        |--------------------------------------------------------------------------
        | POSSESSION STATUS MAPPING
        |--------------------------------------------------------------------------
        */

        $possessionStatusMap = [

            'not available'
                => 'not_possessionable',

            '(non lop) possessionable'
                => 'non_lop_possessionable',

            'under development (possessionable)'
                => 'under_development_possessionable',

            'available'
                => 'possessionable',
        ];


        /*
        |--------------------------------------------------------------------------
        | DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | DISABLE ACTIVITY LOGGING
            |--------------------------------------------------------------------------
            |
            | CSV IMPORT KE DAURAN KOI ACTIVITY LOG NAHI BANAY GA.
            |
            */

            activity()->disableLogging();


            /*
            |--------------------------------------------------------------------------
            | IMPORT ROWS
            |--------------------------------------------------------------------------
            */

            foreach ($rows as $index => $row) {

                try {

                    /*
                    |--------------------------------------------------------------------------
                    | CONVERT ROW TO ASSOCIATIVE ARRAY
                    |--------------------------------------------------------------------------
                    */

                    $data = array_combine(
                        $header,
                        $row
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | 1. PROJECT
                    |--------------------------------------------------------------------------
                    */

                    $project = Project::where(
                        'project_name',
                        trim($data['Project'])
                    )->first();


                    if (! $project) {

                        throw new \Exception(
                            'Project not found'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 2. BLOCK
                    |--------------------------------------------------------------------------
                    */

                    $block = Block::where(
                        'block_name',
                        trim($data['Block'])
                    )
                        ->where(
                            'project_id',
                            $project->id
                        )
                        ->first();


                    if (! $block) {

                        throw new \Exception(
                            'Block not found'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 3. PLOT
                    |--------------------------------------------------------------------------
                    */

                    $plot = Plot::where(
                        'plot_number',
                        trim($data['Plot'])
                    )
                        ->where(
                            'block_id',
                            $block->id
                        )
                        ->first();


                    if (! $plot) {

                        throw new \Exception(
                            'Plot not found'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 4. PREVIOUS AREA
                    |--------------------------------------------------------------------------
                    */

                    $previousArea = AreaVariation::where(
                        'plot_id',
                        $plot->id
                    )
                        ->latest('measured_date')
                        ->value('measured_area');


                    $previousArea = $previousArea ?? 0;


                    /*
                    |--------------------------------------------------------------------------
                    | 5. POSSESSION STATUS
                    |--------------------------------------------------------------------------
                    */

                    $csvPossessionStatus = trim(
                        $data['possession_status'] ?? ''
                    );


                    $possessionStatus =
                        $csvPossessionStatus !== ''
                        ? (
                            $possessionStatusMap[
                                strtolower($csvPossessionStatus)
                            ]
                            ?? null
                        )
                        : null;


                    /*
                    |--------------------------------------------------------------------------
                    | 6. LOP STATUS
                    |--------------------------------------------------------------------------
                    */

                    $lopStatus =
                        strtolower(
                            trim($data['LOP_status'])
                        ) === 'lop'
                        ? 'lop'
                        : 'non_lop';


                    /*
                    |--------------------------------------------------------------------------
                    | 7. ROAD STATUS
                    |--------------------------------------------------------------------------
                    */

                    $roadStatus =
                        strtolower(
                            trim(
                                $data['asphalttstroad_status']
                            )
                        ) === 'completed'
                        ? 'complete'
                        : 'not_complete';


                    /*
                    |--------------------------------------------------------------------------
                    | 8. SEWER STATUS
                    |--------------------------------------------------------------------------
                    */

                    $sewerValue = strtolower(
                        trim(
                            $data['sewer_status']
                        )
                    );


                    $sewerStatus =
                        // $sewerValue === 'constructed'
                        $sewerValue === 'mh constructed'
                        ? 'constructed'
                        : 'not_constructed';


                    /*
                    |--------------------------------------------------------------------------
                    | 9. AREA VARIATION
                    |--------------------------------------------------------------------------
                    */

                    AreaVariation::create([

                        'plot_id'
                            => $plot->id,

                        'previous_area'
                            => $previousArea,

                        'measured_area'
                            => (float) $data['measured_area'],

                        'measured_date'
                            => $this->parseDate(
                                $data['created_at']
                            ),

                        'possession_status'
                            => $possessionStatus,

                        'remarks'
                            => $data['remarks'] ?? null,

                        'source'
                            => $data['source'] ?? null,

                        'workflow_status'
                            => $data['workflow_status'] ?? null,

                        'lop_status_at_time'
                            => $lopStatus,

                        'road_status_at_time'
                            => $roadStatus,

                        'sewer_status_at_time'
                            => $sewerStatus,
                    ]);


                } catch (\Throwable $e) {

                    /*
                    |--------------------------------------------------------------------------
                    | ROW ERROR
                    |--------------------------------------------------------------------------
                    |
                    | Agar kisi aik row mein error ho to poora import
                    | band nahi hoga.
                    |
                    */

                    Log::error(
                        'AreaVariation Import Failed',
                        [
                            'row' =>
                                $index + 2,

                            'reason' =>
                                $e->getMessage(),

                            'data' =>
                                $row,
                        ]
                    );


                    $this->warn(
                        'Row ' .
                        ($index + 2) .
                        ' skipped: ' .
                        $e->getMessage()
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();


            $this->info(
                'Area Variation CSV import completed successfully.'
            );


            return Command::SUCCESS;


        } catch (\Throwable $e) {


            /*
            |--------------------------------------------------------------------------
            | ROLLBACK
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            $this->error(
                'Area Variation CSV import failed: ' .
                $e->getMessage()
            );


            return Command::FAILURE;


        } finally {


            /*
            |--------------------------------------------------------------------------
            | ENABLE ACTIVITY LOGGING AGAIN
            |--------------------------------------------------------------------------
            |
            | Ye bohat important hai.
            |
            | Import ke baad normal website activity logging
            | dobara ON ho jayegi.
            |
            */

            activity()->enableLogging();
        }
    }


    /**
     * Parse CSV date.
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {

            return \Carbon\Carbon::parse($date);

        } catch (\Throwable $e) {

            Log::warning(
                'AreaVariation Import Date Parse Failed',
                [
                    'date' => $date,
                    'reason' => $e->getMessage(),
                ]
            );

            return null;
        }
    }
}
