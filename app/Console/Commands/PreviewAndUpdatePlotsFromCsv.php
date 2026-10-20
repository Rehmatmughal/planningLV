<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;
class PreviewAndUpdatePlotsFromCsv extends Command

{

    protected $signature = 'plots:update-from-csv

        {--type= : size or development}
        {--mode=preview : preview or apply}

        {--file= : CSV file path}

        {--preview= : Preview report path for apply mode}';

    protected $description =

        'Safely preview and apply approved plot updates from CSV';

    private string $updateType = '';

    private function requiredColumnsForType(): array
    {
        $common = [
            'Project', 'Block', 'Street', 'Plot No',
            'Property Type', 'numbering_type',
        ];

        return match ($this->updateType) {
            'size' => [...$common, 'Size'],
            'development' => [...$common, 'sewer_manholes', 'asphalt_tst', 'overall_status'],
            default => throw new RuntimeException('Unsupported update type. Use size or development.'),
        };
    }

    private array $requiredTables = [
        'plots',
        'projects',
        'blocks',
        'streets',
        'property_types',
        'plotsizes',
        'plot_size_assignments',
        'plot_category_types',
        'lop_statuses',
        'mortgage_statuses',
        'plot_coordinates',
        'development_statuses',
    ];

    public function handle(): int

    {

        $this->updateType = strtolower(trim((string) $this->option('type')));

        if (! in_array($this->updateType, ['size', 'development'], true)) {
            $this->error('Please provide --type=size or --type=development.');
            return self::FAILURE;
        }

        $mode = strtolower(trim((string) $this->option('mode')));

        if (! in_array($mode, ['preview', 'apply'], true)) {

            $this->error('Mode must be preview or apply.');

            return self::FAILURE;

        }

        $defaultFile = storage_path(

            'app/03a_GGGR-_List_of_commercial_plots-all-incPropertytype.csv'

        );

        $file = $this->option('file') ?: $defaultFile;
        if (! File::exists($file)) {
            $this->error("CSV file not found: {$file}");
            return self::FAILURE;
        }

        // Safety check: do not run against the live database.

        $database = DB::connection()->getDatabaseName();
        if ($database !== 'project_table-ibechs-temp') {
            $this->error(

                "Safety stop: current database is '{$database}'. "

                . 'Testing must use project_table-ibechs-temp.'


            );

            return self::FAILURE;

        }

        foreach ($this->requiredTables as $table) {

            if (! Schema::hasTable($table)) {

                $this->error("Required table not found: {$table}");

                return self::FAILURE;

            }

        }

        try {

            return $mode === 'preview'

                ? $this->makePreview($file)

                : $this->applyPreview($file);

        } catch (Throwable $e) {

            $this->error('Command failed: ' . $e->getMessage());

            return self::FAILURE;

        }

    }

    private function readCsv(string $file): array

    {

        $handle = fopen($file, 'r');

        if ($handle === false) {

            throw new RuntimeException('Unable to open CSV file.');

        }

        try {

            $header = fgetcsv($handle);

            if (! is_array($header)) {

                throw new RuntimeException('CSV header is missing.');

            }

            $header = array_map(

                fn ($value) => trim((string) $value),

                $header

            );

            if (isset($header[0])) {

                $header[0] = preg_replace(

                    '/^\xEF\xBB\xBF/',

                    '',

                    $header[0]

                );

            }

            if (count($header) !== count(array_unique($header))) {

                throw new RuntimeException(

                    'CSV contains duplicate column headings.'

                );

            }

            foreach ($this->requiredColumnsForType() as $column) {

                if (! in_array($column, $header, true)) {

                    throw new RuntimeException(

                        "Required CSV column is missing: {$column}"

                    );

                }

            }

            $rows = [];

            $sourceRow = 1;

            while (($values = fgetcsv($handle)) !== false) {

                $sourceRow++;

                if ($values === [null] || $values === []) {

                    continue;

                }

                if (count($values) !== count($header)) {

                    throw new RuntimeException(

                        "CSV row {$sourceRow} has an unexpected number "

                        . 'of columns. No update has been performed.'

                    );

                }

                $data = array_combine($header, $values);

                foreach ($data as $key => $value) {

                    $data[$key] = trim((string) ($value ?? ''));

                }

                // Only the selected update task may contribute update values.
                if ($this->updateType !== 'size') {
                    $data['Size'] = '';
                }
                if ($this->updateType !== 'development') {
                    $data['sewer_manholes'] = '';
                    $data['asphalt_tst'] = '';
                    $data['overall_status'] = '';
                }
                foreach (['Category', 'LOP-Status', 'is_Mortgage', 'Easting', 'Northing',
                    'Rotation', 'UTM_E', 'UTM_N', 'Latitude', 'Longitude', 'pid_lv',
                    'possession_status'] as $ignoredColumn) {
                    $data[$ignoredColumn] = '';
                }

                $data['__source_row'] = $sourceRow;

                $rows[] = $data;

            }

            return $rows;

        } finally {

            fclose($handle);

        }

    }

    /**

     * Build every plan first so ALL rows sharing a matching key

     * can be rejected, not just the second occurrence.

     */

    private function buildAllPlans(array $rows): array

    {

        $plans = [];

        $rawKeyCounts = [];

        // First pass: count duplicate source keys before resolving sizes

        // or categories. A row with a missing size must still count.

        foreach ($rows as $data) {

            $type = strtolower(trim($data['numbering_type'] ?? ''));

            if (! in_array($type, ['streetwise', 'blockwise'], true)) {

                continue;

            }

            $parts = [

                trim($data['Project'] ?? ''),

                trim($data['Block'] ?? ''),

                trim($data['Property Type'] ?? ''),

            ];

            if ($type === 'streetwise') {

                $parts[] = trim($data['Street'] ?? '');

            }

            $parts[] = trim($data['Plot No'] ?? '');

            $key = json_encode($parts, JSON_UNESCAPED_UNICODE);

            $rawKeyCounts[$key] = ($rawKeyCounts[$key] ?? 0) + 1;

        }

        // Second pass: build each row's full plan.

        foreach ($rows as $data) {

            try {

                $plan = $this->buildPlan($data);

            } catch (Throwable $e) {

                $plan = $this->issue(

                    $this->basePlan($data),

                    'ROW_ERROR',

                    $e->getMessage()

                );

            }

            $type = strtolower(trim($data['numbering_type'] ?? ''));

            if (in_array($type, ['streetwise', 'blockwise'], true)) {

                $parts = [

                    trim($data['Project'] ?? ''),

                    trim($data['Block'] ?? ''),

                    trim($data['Property Type'] ?? ''),

                ];

                if ($type === 'streetwise') {

                    $parts[] = trim($data['Street'] ?? '');

                }

                $parts[] = trim($data['Plot No'] ?? '');

                $key = json_encode($parts, JSON_UNESCAPED_UNICODE);

                if (($rawKeyCounts[$key] ?? 0) > 1) {

                    $plan['status'] = 'DUPLICATE_CSV_KEY';

                    $plan['message'] =

                        'Duplicate source matching key. All rows with this '

                        . 'key are blocked, including rows with other issues.';

                    $plan['old_values_json'] = '';

                    $plan['new_values_json'] = '';

                }

            }

            $plans[] = $plan;

        }

        return $plans;

    }

    private function makePreview(string $file): int

    {

        $rows = $this->readCsv($file);

        $plans = $this->buildAllPlans($rows);

        $timestamp = now()->format('Ymd_His');

        $report = storage_path(

            "app/plot-update-preview-{$this->updateType}-{$timestamp}.csv"

        );

        $this->writeReport($report, $plans);

        $meta = [

            'source_file' => realpath($file),

            'source_sha256' => hash_file('sha256', $file),

            'report_file' => realpath(dirname($report))

                . DIRECTORY_SEPARATOR . basename($report),

            'database' => DB::connection()->getDatabaseName(),

            'type' => $this->updateType,

            'created_at' => now()->toDateTimeString(),

        ];

        File::put(

            $report . '.meta.json',

            json_encode(

                $meta,

                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES

            )

        );

        $ready = count(array_filter(

            $plans,

            fn ($p) => ($p['status'] ?? '') === 'READY'

        ));

        $issues = count($plans) - $ready;

        $this->info('Preview created. No database records were updated.');

        $this->line("Database: {$meta['database']}");

        $this->line("Report: {$report}");

        $this->line("Ready records: {$ready}");

        $this->line("Records needing review: {$issues}");

        $this->line('Review the report before approving any rows.');

        $this->line('Set Approve to YES only for records you approve.');

        return self::SUCCESS;

    }

    private function basePlan(array $data): array

    {

        return [

            'source_row' => $data['__source_row'] ?? '',

            'approve' => 'NO',

            'status' => 'SKIPPED',

            'message' => '',

            'plot_id' => '',

            'project' => $data['Project'] ?? '',

            'block' => $data['Block'] ?? '',

            'property_type' => $data['Property Type'] ?? '',

            'street' => $data['Street'] ?? '',

            'plot_number' => $data['Plot No'] ?? '',

            'numbering_type' => $data['numbering_type'] ?? '',

            'match_key' => '',

            'old_values_json' => '',

            'new_values_json' => '',

        ];

    }

    private function buildPlan(array $data): array

    {
        $base = $this->basePlan($data);
        $project = DB::table('projects')
            ->where('project_name', $data['Project'])
            ->first();
        if (! $project) {
            return $this->issue($base, 'PROJECT_NOT_FOUND');
        }
        $block = DB::table('blocks')
            ->where('project_id', $project->id)
            ->where('block_name', $data['Block'])
            ->first();
        if (! $block) {
            return $this->issue($base, 'BLOCK_NOT_FOUND');
        }
        $propertyType = DB::table('property_types')
            ->where('name', $data['Property Type'])
            ->first();
        if (! $propertyType) {
            return $this->issue($base, 'PROPERTY_TYPE_NOT_FOUND');
        }

        $numberingType = strtolower(trim($data['numbering_type']));
        if (! in_array($numberingType, ['streetwise', 'blockwise'], true)) {
            return $this->issue(
                $base,
                'INVALID_NUMBERING_TYPE',
                'CSV numbering_type must be streetwise or blockwise.'
            );

        }

        $query = DB::table('plots')
            ->whereNull('deleted_at')
            ->where('project_id', $project->id)
            ->where('block_id', $block->id)
            ->where('property_type_id', $propertyType->id)
            ->where('plot_number', $data['Plot No']);
        $street = null;
        if ($numberingType === 'streetwise') {
            $street = DB::table('streets')
                ->where('project_id', $project->id)
                ->where('block_id', $block->id)
                ->where('street_name', $data['Street'])
                ->first();
            if (! $street) {
                return $this->issue($base, 'STREET_NOT_FOUND');
            }
            $query->where('street_id', $street->id);
        }
        $matches = $query->get();
        if ($matches->count() === 0) {
            return $this->issue($base, 'PLOT_NOT_FOUND');
        }
        if ($matches->count() !== 1) {
            return $this->issue($base, 'AMBIGUOUS_PLOT_MATCH');
        }
        $plot = $matches->first();
        $base['plot_id'] = $plot->id;
        $base['match_key'] = implode('|', [
            $project->id,
            $block->id,
            $propertyType->id,
            $numberingType === 'streetwise'
                ? $street->id
                : 'BLOCKWISE',
            $data['Plot No'],
        ]);
        $old = [];
        $new = [];
        if ($this->updateType === 'size') {
            // Size master and allowed assignment must already exist.
            $this->propose(
                $old,
                $new,
                'size_id',
                $plot->size_id,
                function () use ($data, $project, $block, $propertyType) {
                    if ($data['Size'] === '') {
                        return null;
                    }
                    $size = DB::table('plotsizes')
                        ->whereNull('deleted_at')
                        ->where('project_id', $project->id)
                        ->where('title', $data['Size'])
                        ->first();
                    if (! $size) {
                        throw new RuntimeException('SIZE_NOT_FOUND: ' . $data['Size']);
                    }
                    $assigned = DB::table('plot_size_assignments')
                        ->where('project_id', $project->id)
                        ->where('block_id', $block->id)
                        ->where('property_type_id', $propertyType->id)
                        ->where('plotsize_id', $size->id)
                        ->exists();
                    if (! $assigned) {
                        throw new RuntimeException('SIZE_ASSIGNMENT_MISSING: ' . $data['Size']);
                    }
                    return $size->id;
                }
            );
        }

        if ($this->updateType === 'development') {
            $this->proposeRelated(
                $old,
                $new,
                'development_statuses',
                $plot->id,
                [
                    'sewer_manholes' => 'sewer_manholes',
                    'asphalt_tst' => 'asphalt_tst',
                    'overall_status' => 'overall_status',
                ],
                $data,
                fn ($value) => strtolower(trim($value))
            );
        }

        $base['status'] = 'READY';

        $base['message'] = count($new) > 0

            ? 'Unique match; proposed changes available.'

            : 'No non-blank fields to update.';

        $base['old_values_json'] = json_encode(

            $old,

            JSON_UNESCAPED_SLASHES

        );

        $base['new_values_json'] = json_encode(

            $new,

            JSON_UNESCAPED_SLASHES

        );

        return $base;

    }

    private function propose(

        array &$old,

        array &$new,

        string $field,

        $current,

        callable $resolve

    ): void {

        $value = $resolve();

        // Blank source fields return null and are ignored.

        if ($value === null) {

            return;

        }

        $old[$field] = $current;

        $new[$field] = $value;

    }

    // private function proposeRelated(
    //     array &$old,
    //     array &$new,
    //     string $table,
    //     int $plotId,
    //     array $columnMap,
    //     array $data,
    //     ?callable $transform = null
    // ): void {
    //     $row = DB::table($table)
    //         ->where('plot_id', $plotId)
    //         ->first();
    //     foreach ($columnMap as $csvColumn => $dbColumn) {
    //         $raw = $data[$csvColumn] ?? '';
    //         // Do not overwrite or clear fields that are blank in the CSV.
    //         if ($raw === '') {
    //             continue;
    //         }
    //         $value = $transform
    //             ? $transform($raw)
    //             : $raw;
    //         $old[$table][$dbColumn] = $row?->{$dbColumn};
    //         $new[$table][$dbColumn] = $value;
    //     }
    // }
    
    // private function proposeRelated(
    //     array &$old,
    //     array &$new,
    //     string $table,
    //     int $plotId,
    //     array $columnMap,
    //     array $data,
    //     ?callable $transform = null
    // ): void {
    //     $row = DB::table($table)
    //         ->where('plot_id', $plotId)
    //         ->first();

    //     foreach ($columnMap as $csvColumn => $dbColumn) {
    //         $raw = $data[$csvColumn] ?? '';

    //         // Blank CSV values must not clear existing database values.
    //         if ($raw === '') {
    //             continue;
    //         }

    //         $value = $transform
    //             ? $transform($raw)
    //             : $raw;

    //         // Convert CSV yes/no to the database's allowed sewer values.
    //         if (
    //             $table === 'development_statuses'
    //             && $dbColumn === 'sewer_manholes'
    //         ) {
    //             $value = match (strtolower(trim((string) $value))) {
    //                 'yes' => 'constructed',
    //                 'no' => 'not_constructed',
    //                 default => $value,
    //             };
    //         }

    //         $old[$table][$dbColumn] = $row?->{$dbColumn};
    //         $new[$table][$dbColumn] = $value;
    //     }
    // }

    private function proposeRelated(
        array &$old,
        array &$new,
        string $table,
        int $plotId,
        array $columnMap,
        array $data,
        ?callable $transform = null
    ): void {
        $row = DB::table($table)
            ->where('plot_id', $plotId)
            ->first();

        foreach ($columnMap as $csvColumn => $dbColumn) {
            $raw = $data[$csvColumn] ?? '';

            // Blank CSV values must not clear existing database values.
            if ($raw === '') {
                continue;
            }

            $value = $transform
                ? $transform($raw)
                : $raw;

            // Convert CSV yes/no to the database's allowed sewer values.
            if (
                $table === 'development_statuses'
                && $dbColumn === 'sewer_manholes'
            ) {
                $value = match (strtolower(trim((string) $value))) {
                    'yes' => 'constructed',
                    'no' => 'not_constructed',
                    default => $value,
                };
            }

            $old[$table][$dbColumn] = $row?->{$dbColumn};
            $new[$table][$dbColumn] = $value;
        }
    }


    private function issue(
        array $base,
        string $status,
        string $message = ''
    ): array {
        $base['status'] = $status;
        $base['message'] = $message !== ''
            ? $message
            : $status;
        return $base;
    }

    private function writeReport(string $path, array $plans): void

    {

        $handle = fopen($path, 'w');

        if ($handle === false) {

            throw new RuntimeException(

                'Unable to create preview report.'
            );
        }
        try {
            fputcsv($handle, [
                'Source Row',
                'Approve',
                'Status',
                'Message',
                'Plot ID',
                'Project',
                'Block',
                'Property Type',
                'Street',
                'Plot Number',
                'Numbering Type',
                'Match Key',
                'Old Values JSON',
                'Proposed Values JSON',
            ]);
            foreach ($plans as $plan) {
                fputcsv($handle, [
                    $plan['source_row'],
                    $plan['approve'],
                    $plan['status'],
                    $plan['message'],
                    $plan['plot_id'],
                    $plan['project'],
                    $plan['block'],
                    $plan['property_type'],
                    $plan['street'],
                    $plan['plot_number'],
                    $plan['numbering_type'],
                    $plan['match_key'],
                    $plan['old_values_json'],
                    $plan['new_values_json'],
                ]);
            }
        } finally {
            fclose($handle);
        }
    }

    private function applyPreview(string $file): int
    {
        $report = $this->option('preview');
        if (! $report || ! File::exists($report)) {
            $this->error(
                'Apply requires --preview with the exact preview report path.'
            );
            return self::FAILURE;
        }

        $metaPath = $report . '.meta.json';
        if (! File::exists($metaPath)) {
            $this->error('Preview metadata file is missing.');
            return self::FAILURE;
        }
        $meta = json_decode(File::get($metaPath), true);
        if (! is_array($meta)) {
            $this->error('Preview metadata is invalid.');
            return self::FAILURE;
        }

        if (($meta['type'] ?? '') !== $this->updateType) {
            $this->error('Preview type does not match --type. Generate a new preview.');
            return self::FAILURE;
        }

        if (($meta['database'] ?? '') !== 'project_table-ibechs-temp') {
            $this->error(
                'Preview was not generated for the temporary database.'
            );
            return self::FAILURE;
        }
        if (
            ($meta['source_sha256'] ?? '') !== hash_file('sha256', $file)
        ) {
            $this->error(
                'Source CSV has changed since preview. Generate a new preview.'
            );
            return self::FAILURE;
        }
        $handle = fopen($report, 'r');
        if ($handle === false) {
            $this->error('Unable to read preview report.');
            return self::FAILURE;
        }

        $approved = [];

        try {

            $header = fgetcsv($handle);

            if (! is_array($header)) {

                throw new RuntimeException(

                    'Preview report header is missing.'

                );

            }

            while (($values = fgetcsv($handle)) !== false) {

                if (count($values) !== count($header)) {

                    throw new RuntimeException(

                        'Preview report has an invalid row.'

                    );

                }

                $row = array_combine($header, $values);

                if (strtoupper(trim($row['Approve'] ?? 'NO')) !== 'YES') {

                    continue;

                }

                if (($row['Status'] ?? '') !== 'READY') {

                    throw new RuntimeException(

                        'Only READY records can be approved. Source row: '

                        . ($row['Source Row'] ?? '?')

                    );

                }

                $sourceRow = (int) ($row['Source Row'] ?? 0);

                if ($sourceRow < 2 || isset($approved[$sourceRow])) {

                    throw new RuntimeException(

                        'Invalid or repeated source row in preview report.'

                    );

                }

                $approved[$sourceRow] = $row;

            }

        } finally {

            fclose($handle);

        }

        if ($approved === []) {

            $this->warn('No records approved. Nothing was updated.');

            return self::SUCCESS;

        }

        $transactionStarted = false;

        try {

            $rows = $this->readCsv($file);

            // Rebuild ALL plans to detect duplicates again at apply time.

            $freshPlans = $this->buildAllPlans($rows);

            $freshBySourceRow = [];

            foreach ($freshPlans as $plan) {

                $freshBySourceRow[(int) $plan['source_row']] = $plan;

            }

            DB::beginTransaction();

            $transactionStarted = true;

            foreach ($approved as $sourceRow => $reportRow) {

                $fresh = $freshBySourceRow[$sourceRow] ?? null;

                if (

                    ! $fresh

                    || ($fresh['status'] ?? '') !== 'READY'

                    || (string) $fresh['plot_id']

                        !== (string) $reportRow['Plot ID']

                    || $fresh['match_key'] !== $reportRow['Match Key']

                    || $fresh['old_values_json']

                        !== $reportRow['Old Values JSON']

                    || $fresh['new_values_json']

                        !== $reportRow['Proposed Values JSON']

                ) {

                    throw new RuntimeException(

                        "Source row {$sourceRow} changed since preview, "

                        . 'or is no longer safe to update. '

                        . 'Generate a new preview.'

                    );

                }

                $newValues = json_decode(

                    $fresh['new_values_json'],

                    true

                );

                if (! is_array($newValues)) {

                    throw new RuntimeException(

                        "Invalid proposed values for row {$sourceRow}."

                    );

                }

                $this->applyValues(

                    (int) $fresh['plot_id'],

                    $newValues

                );

            }

            DB::commit();

            $transactionStarted = false;

            $this->info(

                'Approved records updated successfully: '

                . count($approved)

            );

            return self::SUCCESS;

        } catch (Throwable $e) {

            if ($transactionStarted) {

                DB::rollBack();

            }

            $this->error(

                'Apply failed; transaction rolled back: '

                . $e->getMessage()

            );

            return self::FAILURE;

        }

    }

    private function applyValues(int $plotId, array $values): void

    {

        $allowed = $this->updateType === 'size'
            ? ['size_id']
            : ['development_statuses'];

        foreach (array_keys($values) as $key) {
            if (! in_array($key, $allowed, true)) {
                throw new RuntimeException("Blocked field '{$key}' for update type '{$this->updateType}'.");
            }
        }

        $plotValues = [];

        foreach (

            ['size_id']

            as $field

        ) {

            if (array_key_exists($field, $values)) {

                $plotValues[$field] = $values[$field];

            }

        }

        if ($plotValues !== []) {

            DB::table('plots')

                ->where('id', $plotId)

                ->update($plotValues + ['updated_at' => now()]);

        }

        foreach ([

            'development_statuses',

        ] as $table) {

            if (empty($values[$table])) {

                continue;

            }

            $updates = $values[$table];

            $existing = DB::table($table)

                ->where('plot_id', $plotId)

                ->first();

            if ($existing) {

                DB::table($table)

                    ->where('plot_id', $plotId)

                    ->update($updates + ['updated_at' => now()]);

            } else {

                DB::table($table)->insert(

                    $updates + [

                        'plot_id' => $plotId,

                        'created_at' => now(),

                        'updated_at' => now(),

                    ]

                );

            }

        }

    }

}
