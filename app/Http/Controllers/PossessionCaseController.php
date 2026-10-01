<?php

namespace App\Http\Controllers;

use App\Models\PossessionCase;
use App\Models\Project;
use App\Models\Block;
use App\Models\Street;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Owner;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;



class PossessionCaseController extends Controller
{
    /**
     * Upload historical possession file.
     *
     * This stage only stores the uploaded file temporarily.
     * No possession record is created yet.
     */
    public function import(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate uploaded file
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
                'max:10240',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'property_type_id' => [
                'required',
                'integer',
                'exists:property_types,id',
            ],

            'owner_action' => [
                'required',
                Rule::in([
                    'update',
                    'keep',
                ]),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Store file temporarily
        |--------------------------------------------------------------------------
        */

        $file = $request->file('file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $fileName =
            'possession-import-' .
            Str::uuid() .
            '.' .
            $extension;


        $path = $file->storeAs(
            'imports/possession',
            $fileName,
            'local'
        );


        /*
        |--------------------------------------------------------------------------
        | Save import settings in session
        |--------------------------------------------------------------------------
        */

        session([
            'possession_import' => ['path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'owner_action' => $validated['owner_action'],
                'project_id' => $validated['project_id'],
                'property_type_id' => $validated['property_type_id'],
            ],
        ]);
        /*
        |--------------------------------------------------------------------------
        | Go to preview
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('possession-cases.import.preview')
            ->with(
                'success',
                'Possession file uploaded successfully. Please review the preview before importing.'
            );
    }
    /**
     * Preview uploaded historical possession file.
     *
     * No database records are created here.
     */

    /**
     * Preview uploaded historical possession file.
     *
     * Only the header and first 20 data rows are read.
     * No database records are created here.
     */
    public function importPreview()
    {
        /*
        |--------------------------------------------------------------------------
        | Get uploaded import information
        |--------------------------------------------------------------------------
        */

        $import = session('possession_import');


        if (!$import || empty($import['path'])) {

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'No possession import file was found. Please upload the file again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Check file still exists
        |--------------------------------------------------------------------------
        */

        if (!Storage::disk('local')->exists($import['path'])) {

            session()->forget('possession_import');

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The uploaded possession file is no longer available. Please upload it again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Get physical file path
        |--------------------------------------------------------------------------
        */

        $fullPath = Storage::disk('local')
            ->path($import['path']);


        /*
        |--------------------------------------------------------------------------
        | Load spreadsheet
        |--------------------------------------------------------------------------
        */

        $spreadsheet = IOFactory::load($fullPath);

        $worksheet = $spreadsheet->getActiveSheet();


        /*
        |--------------------------------------------------------------------------
        | Get worksheet dimensions
        |--------------------------------------------------------------------------
        */

        $highestColumn =
            $worksheet->getHighestColumn();

        $highestRow =
            $worksheet->getHighestRow();


        /*
        |--------------------------------------------------------------------------
        | Read only first 21 rows
        |
        | Row 1     = Header
        | Rows 2-21 = First 20 data rows
        |--------------------------------------------------------------------------
        */

        $previewLastRow = min(
            $highestRow,
            21
        );


        $rows = $worksheet->rangeToArray(
            'A1:' . $highestColumn . $previewLastRow,
            null,
            true,
            true,
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Prepare headers
        |--------------------------------------------------------------------------
        */

        $headers = [];


        if (isset($rows[1])) {

            foreach ($rows[1] as $header) {

                $headers[] = trim(
                    (string) $header
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Prepare preview rows
        |--------------------------------------------------------------------------
        */

        $previewRows = [];


        foreach ($rows as $rowNumber => $row) {

            /*
            | Skip header row
            */
            if ($rowNumber === 1) {
                continue;
            }


            $previewRows[] = array_values($row);
        }


        /*
        |--------------------------------------------------------------------------
        | Total data rows
        |--------------------------------------------------------------------------
        */

        $totalRows = max(
            $highestRow - 1,
            0
        );


        /*
        |--------------------------------------------------------------------------
        | Free spreadsheet memory
        |--------------------------------------------------------------------------
        */

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);


        /*
        |--------------------------------------------------------------------------
        | Return preview page
        |--------------------------------------------------------------------------
        */

        return view(
            'possession_cases.import_preview',
            compact(
                'headers',
                'previewRows',
                'totalRows',
                'import'
            )
        );
    }

    /**
     * Validate uploaded historical possession file.
     *
     * IMPORTANT:
     * This method DOES NOT create or update any database record.
     *
     * It only validates:
     * - Project
     * - Block
     * - Plot
     * - Possession number
     * - Possession sequence
     * - Revision
     * - Owner count
     * - Owner names
     * - Owner CNIC/NTN
     * - Existing owners
     * - Duplicate possessions
     * - Date
     * - Reference number
     */
    public function validateImport()
    {
        $import = session('possession_import');
        if (!$import || empty($import['path'])) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'No possession import file was found. Please upload the file again.',
                ]);
        }
        // |--------------------------------------------------------------------------
        // | Check uploaded file
        // |--------------------------------------------------------------------------
        if (!Storage::disk('local')->exists($import['path'])) {

            session()->forget('possession_import');

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The uploaded possession file is no longer available. Please upload it again.',
                ]);
        }
        // |--------------------------------------------------------------------------
        // | Project
        // |--------------------------------------------------------------------------
        $projectId = $import['project_id'] ?? null;
        if (!$projectId) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'project_id' =>
                        'Project was not selected for the possession import.',
                ]);
        }
        $propertyTypeId = $import['property_type_id'] ?? null;
        if (!$propertyTypeId) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'property_type_id' =>
                        'Property Type was not selected for this possession import.',
                ]);
        }

        $project = Project::find($projectId);
        if (!$project) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'project_id' =>
                        'Selected project was not found.',
                ]);
        }
        // |--------------------------------------------------------------------------
        // | Load Spreadsheet
        // |--------------------------------------------------------------------------
        $fullPath = Storage::disk('local')
            ->path($import['path']);
        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();

        // |--------------------------------------------------------------------------
        // | Spreadsheet information
        // |--------------------------------------------------------------------------

        $highestColumn =
            $worksheet->getHighestColumn();
        $highestRow =
            $worksheet->getHighestRow();
        /*
        |--------------------------------------------------------------------------
        // | Read complete file
        |--------------------------------------------------------------------------
        |
        | Unlike preview, here we intentionally read all rows because
        | validation has to check the complete file.
        |
        */
        $rows = $worksheet->rangeToArray(
            'A1:' . $highestColumn . $highestRow,
            null,
            true,
            true,
            true
        );
        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */
        $headerRow = $rows[1] ?? [];
        /*
        |--------------------------------------------------------------------------
        | Keep the ORIGINAL spreadsheet column key
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | A => PossessionNo.
        | B => Helper
        | C => Ref No.
        | D => Name
        |
        | rangeToArray(..., true) preserves these keys.
        |
        */
        $headers = [];
        $headerMap = [];
        foreach ($headerRow as $columnKey => $header) {
            $header = trim(
                (string) $header
            );
            if ($header === '') {
                continue;
            }
            $headers[] = $header;
            $headerMap[
                mb_strtolower($header)
            ] = $columnKey;
        }
        /*
        |--------------------------------------------------------------------------
        | Required CSV columns
        |--------------------------------------------------------------------------
        */
        $requiredColumns = [
            'PossessionNo.',
            'Helper',
            'Ref No.',
            'Name',
            'S/o, W/o,D/o',
            'Plot Numbers',
            'Block',
            'CNC/NTN NO.',
            'Address',
            'Date Possession Hand Over',
            'Contact No',
            'Need Approval',
        ];
        /*
        |--------------------------------------------------------------------------
        | Check required columns
        |--------------------------------------------------------------------------
        */
        $missingColumns = [];
        foreach ($requiredColumns as $requiredColumn) {
            $key = mb_strtolower(
                trim($requiredColumn)
            );
            if (!array_key_exists(
                $key,
                $headerMap
            )) {
                $missingColumns[] =
                    $requiredColumn;
            }
        }
        if (!empty($missingColumns)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The uploaded file is missing required columns: '
                        . implode(', ', $missingColumns),
                ]);
        }
        $missingColumns = [];
        foreach ($requiredColumns as $requiredColumn) {
            $key = mb_strtolower(
                trim($requiredColumn)
            );
            if (!array_key_exists($key, $headerMap)) {
                $missingColumns[] =
                    $requiredColumn;
            }
        }
        if (!empty($missingColumns)) {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The uploaded file is missing required columns: '
                        . implode(', ', $missingColumns),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Helper function to get cell by column name
        |--------------------------------------------------------------------------
        */

        $getCell = function (
            array $row,
            string $column
          ) use ($headerMap) {

            $key = mb_strtolower(
                trim($column)
            );

            $columnKey =
                $headerMap[$key] ?? null;

            if ($columnKey === null) {
                return '';
            }

            return trim(
                (string) (
                    $row[$columnKey] ?? ''
                )
            );
        };
        // | Load all project plots
        // | We load them once instead of running a database query for
        // | every single CSV row.
        $plots = Plot::where(
            'project_id',
            $projectId
        )
        ->where(
            'property_type_id',
            $propertyTypeId
        )
            ->get([
                'id',
                'block_id',
                'plot_number',
                'property_type_id',
            ]);

        // | Plot lookup map
        $plotMap = [];

        foreach ($plots as $plot) {

            $plotNumberKey =
                mb_strtoupper(
                    trim((string) $plot->plot_number)
                );

            $plotMap[
                $plot->block_id
                . '|'
                . $plot->property_type_id
                . '|'
                . $plotNumberKey
            ] = $plot;
        }

        // | Load Blocks
        
        $blocks = Block::where(
            'project_id',
            $projectId
        )
            ->get([
                'id',
                'block_name',
            ]);


        $blockMap = [];

        foreach ($blocks as $block) {

            $blockMap[
                mb_strtoupper(
                    trim($block->block_name)
                )
            ] = $block;
        }

        // | Existing possession records
        // | withTrashed() is important.
        // | A soft-deleted possession number must also remain reserved.
        $existingPossessions = PossessionCase::withTrashed()
            ->whereHas('plot', function ($query) use ($projectId) {

                $query->withTrashed()
                    ->where(
                        'project_id',
                        $projectId
                    );
            })
            ->get([
                'id',
                'plot_id',
                'possession_no',
            ]);
        $existingPossessionMap = [];
        foreach ($existingPossessions as $case) {
            $existingPossessionMap[
                $case->plot_id . '|' .
                mb_strtoupper(
                    trim($case->possession_no)
                )
            ] = true;
        }
        // | Collect all CNICs first

        $allCnics = [];

        foreach ($rows as $rowNumber => $row) {

            if ($rowNumber === 1) {
                continue;
            }

            $cnicText =
                $getCell(
                    $row,
                    'CNC/NTN NO.'
                );

            $cnics =
                $this->splitImportLines(
                    $cnicText
                );

            foreach ($cnics as $cnic) {

                $normalized =
                    $this->normalizeImportCnic(
                        $cnic
                    );

                if ($normalized !== '') {

                    $allCnics[] =
                        $normalized;
                }
            }
        }
        $allCnics =
            array_values(
                array_unique($allCnics)
            );

        // | Load existing owners in ONE query
        $owners = collect();

        if (!empty($allCnics)) {

            $owners = Owner::whereIn(
                'cnic',
                $allCnics
            )->get();
        }
        $ownerMap = [];

        foreach ($owners as $owner) {

            $ownerMap[
                $this->normalizeImportCnic(
                    $owner->cnic
                )
            ] = $owner;
        }
        // | Validation result containers

        $validationRows = [];

        $validCount = 0;
        $warningCount = 0;
        $errorCount = 0;
        // | Duplicate tracker inside uploaded file
        $fileDuplicateMap = [];

        // | Validate every row

        foreach ($rows as $rowNumber => $row) {

            // | Header
            if ($rowNumber === 1) {
                continue;
            }
            /*
            |--------------------------------------------------------------------------
            | Empty row
            |--------------------------------------------------------------------------
            */
            $rowValues = array_values($row);

            $hasData = false;

            foreach ($rowValues as $value) {

                if (trim((string) $value) !== '') {

                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) {
                continue;
            }
            // | Basic values

            $possessionNo =
                $getCell(
                    $row,
                    'PossessionNo.'
                );

            $helper =
                $getCell(
                    $row,
                    'Helper'
                );

            $referenceNo =
                $getCell(
                    $row,
                    'Ref No.'
                );

            $ownerNamesText =
                $getCell(
                    $row,
                    'Name'
                );

            $relativeNamesText =
                $getCell(
                    $row,
                    'S/o, W/o,D/o'
                );

            $plotNumber =
                $getCell(
                    $row,
                    'Plot Numbers'
                );

            $blockName =
                $getCell(
                    $row,
                    'Block'
                );

            $cnicText =
                $getCell(
                    $row,
                    'CNC/NTN NO.'
                );

            $address =
                $getCell(
                    $row,
                    'Address'
                );

            $dateText =
                $getCell(
                    $row,
                    'Date Possession Hand Over'
                );

            $contactNo =
                $getCell(
                    $row,
                    'Contact No'
                );

            $needApproval =
                $getCell(
                    $row,
                    'Need Approval'
                );


            /*
            |--------------------------------------------------------------------------
            | Row result
            |--------------------------------------------------------------------------
            */

            $errors = [];
            $warnings = [];


            /*
            |--------------------------------------------------------------------------
            | Possession number
            |--------------------------------------------------------------------------
            */

            $parsedPossession =
                $this->parseHistoricalPossessionNumber(
                    $possessionNo
                );


            if (!$parsedPossession) {

                $errors[] =
                    'Invalid PossessionNo. format. Expected formats such as 300 or 300-T1.';
            }


            /*
            |--------------------------------------------------------------------------
            | Owner count
            |--------------------------------------------------------------------------
            */

            $expectedOwnerCount = null;

            if (
                preg_match(
                    '/(\d+)/',
                    $helper,
                    $matches
                )
            ) {

                $expectedOwnerCount =
                    (int) $matches[1];

            } else {

                $errors[] =
                    'Helper value is invalid. Expected values such as 1Owner, 2Owner, 3Owner.';
            }


            /*
            |--------------------------------------------------------------------------
            | Split owner fields
            |--------------------------------------------------------------------------
            */

            $ownerNames =
                $this->splitImportLines(
                    $ownerNamesText
                );

            $relativeNames =
                $this->splitImportLines(
                    $relativeNamesText
                );

            $cnics =
                $this->splitImportLines(
                    $cnicText
                );


            /*
            |--------------------------------------------------------------------------
            | Owner count validation
            |--------------------------------------------------------------------------
            */

            if ($expectedOwnerCount !== null) {

                if (
                    count($ownerNames)
                    !== $expectedOwnerCount
                ) {

                    $errors[] =
                        "Owner count mismatch: Helper says {$expectedOwnerCount} owner(s), but "
                        . count($ownerNames)
                        . " name(s) found.";
                }


                if (
                    count($cnics)
                    !== $expectedOwnerCount
                ) {

                    $errors[] =
                        "Owner count mismatch: Helper says {$expectedOwnerCount} owner(s), but "
                        . count($cnics)
                        . " CNIC/NTN value(s) found.";
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Every owner must have Name + CNIC
            |--------------------------------------------------------------------------
            */

            $parsedOwners = [];

            $ownerLoopCount =
                max(
                    count($ownerNames),
                    count($cnics)
                );


            for (
                $ownerIndex = 0;
                $ownerIndex < $ownerLoopCount;
                $ownerIndex++
            ) {

                $ownerName =
                    trim(
                        $ownerNames[$ownerIndex]
                        ?? ''
                    );

                $relativeName =
                    trim(
                        $relativeNames[$ownerIndex]
                        ?? ''
                    );

                $cnic =
                    $this->normalizeImportCnic(
                        $cnics[$ownerIndex]
                        ?? ''
                    );


                if ($ownerName === '') {

                    $errors[] =
                        'Owner #' .
                        ($ownerIndex + 1) .
                        ' is missing Name.';
                }


                if ($cnic === '') {

                    $errors[] =
                        'Owner #' .
                        ($ownerIndex + 1) .
                        ' is missing CNIC/NTN.';
                }


                /*
                |--------------------------------------------------------------------------
                | Existing owner
                |--------------------------------------------------------------------------
                */

                $existingOwner = null;

                if ($cnic !== '') {

                    $existingOwner =
                        $ownerMap[$cnic]
                        ?? null;
                }


                $parsedOwners[] = [
                    'owner_name' =>
                        $ownerName,

                    'relative_name' =>
                        $relativeName !== ''
                            ? $relativeName
                            : null,

                    'cnic' =>
                        $cnic,

                    'existing_owner_id' =>
                        $existingOwner?->id,

                    'existing_owner_name' =>
                        $existingOwner?->owner_name,

                    'address' =>
                        $expectedOwnerCount === 1
                            ? ($address ?: null)
                            : null,

                    'contact_no' =>
                        $expectedOwnerCount === 1
                            ? ($contactNo ?: null)
                            : null,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Block validation
            |--------------------------------------------------------------------------
            */

            $block = null;

            if ($blockName === '') {

                $errors[] =
                    'Block is missing.';

            } else {

                $block =
                    $blockMap[
                        mb_strtoupper(
                            trim($blockName)
                        )
                    ] ?? null;


                if (!$block) {

                    $errors[] =
                        "Block '{$blockName}' was not found in the selected project.";
                }
            }
            // |--------------------------------------------------------------------------
            // | Plot validation
            // |--------------------------------------------------------------------------

            $plot = null;

            if ($block && $plotNumber !== '') {

                $plotKey =
                    $block->id
                    . '|'
                    . $propertyTypeId
                    . '|'
                    . mb_strtoupper(
                        trim($plotNumber)
                    );

                $plot =
                    $plotMap[$plotKey]
                    ?? null;


                if (!$plot) {

                    $errors[] =
                        "Plot '{$plotNumber}' was not found in Block '{$blockName}' for the selected Property Type.";

                }

            } elseif ($plotNumber === '') {

                $errors[] =
                    'Plot Number is missing.';
            }

            /*
            |--------------------------------------------------------------------------
            | Existing possession / duplicate validation
            |--------------------------------------------------------------------------
            */

            if (
                $plot &&
                $parsedPossession
            ) {

                $possessionKey =
                    $plot->id . '|' .
                    mb_strtoupper(
                        $parsedPossession['possession_no']
                    );


                /*
                | Duplicate inside uploaded file
                */

                if (
                    isset(
                        $fileDuplicateMap[$possessionKey]
                    )
                ) {

                    $errors[] =
                        'Duplicate possession number found in the uploaded file for the same plot.';

                } else {

                    $fileDuplicateMap[
                        $possessionKey
                    ] = true;
                }


                /*
                | Already exists in database
                */

                if (
                    isset(
                        $existingPossessionMap[
                            $possessionKey
                        ]
                    )
                ) {

                    $errors[] =
                        'This possession number already exists for this plot in the database.';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Date validation
            |--------------------------------------------------------------------------
            */

            $receivedAt = null;

            if ($dateText !== '') {

                try {

                    $receivedAt =
                        Carbon::createFromFormat(
                            'd-M-y',
                            trim($dateText)
                        )->format('Y-m-d');

                } catch (\Throwable $e) {

                    $errors[] =
                        "Invalid possession hand-over date '{$dateText}'. Expected format like 01-May-15.";
                }

            } else {

                $warnings[] =
                    'Possession hand-over date is empty.';
            }


            /*
            |--------------------------------------------------------------------------
            | Need Approval
            |--------------------------------------------------------------------------
            */

            $needApprovalValue = false;

            if (
                $needApproval !== ''
                &&
                !in_array(
                    mb_strtolower($needApproval),
                    [
                        'no',
                        '0',
                        'false',
                    ],
                    true
                )
            ) {

                $needApprovalValue = true;
            }


            /*
            |--------------------------------------------------------------------------
            | Multiple-owner address rule
            |--------------------------------------------------------------------------
            */

            if (
                $expectedOwnerCount !== null
                &&
                $expectedOwnerCount > 1
                &&
                $address !== ''
            ) {

                $warnings[] =
                    'Address was found but will be ignored because this possession has multiple owners.';
            }


            /*
            |--------------------------------------------------------------------------
            | Existing owner information
            |--------------------------------------------------------------------------
            */

            $existingOwnerCount = 0;
            $newOwnerCount = 0;

            foreach ($parsedOwners as $parsedOwner) {

                if (
                    !empty(
                        $parsedOwner['existing_owner_id']
                    )
                ) {

                    $existingOwnerCount++;

                } else {

                    $newOwnerCount++;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Final row status
            |--------------------------------------------------------------------------
            */

            if (!empty($errors)) {

                $status = 'error';

                $errorCount++;

            } elseif (!empty($warnings)) {

                $status = 'warning';

                $warningCount++;

            } else {

                $status = 'valid';

                $validCount++;
            }


            /*
            |--------------------------------------------------------------------------
            | Store validation row
            |--------------------------------------------------------------------------
            */

            $validationRows[] = [

                'row_number' =>
                    $rowNumber,

                'status' =>
                    $status,

                'errors' =>
                    $errors,

                'warnings' =>
                    $warnings,

                'possession_no' =>
                    $parsedPossession['possession_no']
                    ?? $possessionNo,

                'base_possession_no' =>
                    $parsedPossession['base_possession_no']
                    ?? null,

                'possession_sequence' =>
                    $parsedPossession['possession_sequence']
                    ?? null,

                'revision_no' =>
                    $parsedPossession['revision_no']
                    ?? null,

                'reference_no' =>
                    $referenceNo !== ''
                        ? $referenceNo
                        : null,

                'plot_id' =>
                    $plot?->id,

                'property_type_id' =>
                    $propertyTypeId,

                'block_id' =>
                    $block?->id,

                'block_name' =>
                    $blockName,

                'plot_number' =>
                    $plotNumber,

                'owner_count' =>
                    count($parsedOwners),

                'existing_owner_count' =>
                    $existingOwnerCount,

                'new_owner_count' =>
                    $newOwnerCount,

                'owners' =>
                    $parsedOwners,

                'received_at' =>
                    $receivedAt,

                'need_approval' =>
                    $needApprovalValue,

                'remarks' =>
                    null,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Save validation result to temporary JSON file
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | We do NOT put 5,364 rows into Laravel session.
        |
        | Otherwise cookie/file session can become very large.
        |
        */

        $validationFileName =
            'possession-validation-' .
            Str::uuid() .
            '.json';


        $validationPath =
            'imports/possession/' .
            $validationFileName;


        Storage::disk('local')->put(
            $validationPath,
            json_encode(
                [
                    'project_id' =>
                        $projectId,

                    'project_name' =>
                        $project->project_name,

                    'source_file' =>
                        $import['original_name'],

                    'owner_action' =>
                        $import['owner_action'],

                    'valid_count' =>
                        $validCount,

                    'warning_count' =>
                        $warningCount,

                    'error_count' =>
                        $errorCount,

                    'total_count' =>
                        count($validationRows),

                    'rows' =>
                        $validationRows,
                ],
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Save only validation file path in session
        |--------------------------------------------------------------------------
        */

        session([
            'possession_import_validation' => [
                'path' =>
                    $validationPath,
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Free spreadsheet memory
        |--------------------------------------------------------------------------
        */

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);


        /*
        |--------------------------------------------------------------------------
        | Redirect to validation result
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'possession-cases.import.validation-result'
            );
    }
    /**
     * Split multiline import value.
     */
    private function splitImportLines(?string $value): array
    {
        if ($value === null) {
            return [];
        }

        $value = str_replace(
            ["\r\n", "\r"],
            "\n",
            $value
        );

        $lines = explode(
            "\n",
            $value
        );

        $result = [];

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line !== '') {
                $result[] = $line;
            }
        }

        return $result;
    }
    /**
     * Normalize CNIC / NTN value coming from import.
     *
     * Examples:
     * 61101-1380278-3 -> 61101-1380278-3
     * 6110113802783   -> 61101-1380278-3
     *
     * Other non-standard values such as NTN are preserved
     * instead of being silently rejected.
     */
    private function normalizeImportCnic(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim($value);

        if ($value === '') {
            return '';
        }

        /*
        | Remove spaces.
        */
        $value = preg_replace(
            '/\s+/',
            '',
            $value
        );

        /*
        | If standard 13-digit CNIC without dashes:
        | 6110113802783
        |
        | convert to:
        | 61101-1380278-3
        */
        if (
            preg_match(
                '/^\d{13}$/',
                $value
            )
        ) {

            return substr($value, 0, 5)
                . '-'
                . substr($value, 5, 7)
                . '-'
                . substr($value, 12, 1);
        }

        return $value;
    }

    /**
     * Show historical possession validation result.
     */
    public function importValidationResult()
    {
        $validation =
            session(
                'possession_import_validation'
            );

        if (
            !$validation ||
            empty($validation['path'])
        ) {

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'No possession validation result was found. Please upload the file again.',
                ]);
        }


        if (
            !Storage::disk('local')->exists(
                $validation['path']
            )
        ) {

            session()->forget(
                'possession_import_validation'
            );

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The possession validation result is no longer available. Please upload the file again.',
                ]);
        }


        $json =
            Storage::disk('local')->get(
                $validation['path']
            );


        $validationData =
            json_decode(
                $json,
                true
            );


        if (!is_array($validationData)) {

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The possession validation result could not be read.',
                ]);
        }


        return view(
            'possession_cases.import_validation',
            [
                'validation' =>
                    $validationData,
            ]
        );
    }
    /**
     * Execute validated historical possession import.
     */
    public function executeImport()
    {
        /*
        |--------------------------------------------------------------------------
        | Get validation result
        |--------------------------------------------------------------------------
        */

        $validation = session(
            'possession_import_validation'
        );

        if (
            !$validation ||
            empty($validation['path'])
        ) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'No validated possession import was found. Please upload and validate the file again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Check validation JSON
        |--------------------------------------------------------------------------
        */

        if (
            !Storage::disk('local')->exists(
                $validation['path']
            )
        ) {
            session()->forget(
                'possession_import_validation'
            );

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The possession validation result is no longer available. Please validate the file again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Read validation JSON
        |--------------------------------------------------------------------------
        */

        $json =
            Storage::disk('local')->get(
                $validation['path']
            );


        $validationData =
            json_decode(
                $json,
                true
            );


        if (
            !is_array($validationData)
        ) {
            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'file' =>
                        'The possession validation result could not be read.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Do NOT import if validation contains errors
        |--------------------------------------------------------------------------
        */

        $errorCount =
            (int) (
                $validationData['error_count']
                ?? 0
            );


        if ($errorCount > 0) {

            return redirect()
                ->route(
                    'possession-cases.import.validation-result'
                )
                ->withErrors([
                    'file' =>
                        'Import cannot continue because the validation result contains errors.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Basic validation information
        |--------------------------------------------------------------------------
        */

        $projectId =
            $validationData['project_id']
            ?? null;


        $ownerAction =
            $validationData['owner_action']
            ?? 'keep';


        $rows =
            $validationData['rows']
            ?? [];


        if (!$projectId) {

            return redirect()
                ->route('possession-cases.index')
                ->withErrors([
                    'project_id' =>
                        'Project information was not found in the validation result.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Counters
        |--------------------------------------------------------------------------
        */

        $importedCount = 0;

        $createdOwnerCount = 0;

        $updatedOwnerCount = 0;

        $existingOwnerCount = 0;


        /*
        |--------------------------------------------------------------------------
        | Import
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Entire import is inside one DB transaction.
        |
        | Agar serious error aaye to partial import nahi hoga.
        |
        */

        try {

            DB::transaction(function () use (
                $projectId,
                $ownerAction,
                $rows,
                &$importedCount,
                &$createdOwnerCount,
                &$updatedOwnerCount,
                &$existingOwnerCount
            ) {

                /*
                |--------------------------------------------------------------------------
                | Process every validated row
                |--------------------------------------------------------------------------
                */

                foreach ($rows as $validationRow) {

                    /*
                    |--------------------------------------------------------------------------
                    | Only valid / warning rows
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !in_array(
                            $validationRow['status'] ?? null,
                            [
                                'valid',
                                'warning',
                            ],
                            true
                        )
                    ) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Required validated values
                    |--------------------------------------------------------------------------
                    */

                    $plotId =
                        $validationRow['plot_id']
                        ?? null;


                    $possessionNo =
                        $validationRow['possession_no']
                        ?? null;


                    if (
                        !$plotId ||
                        !$possessionNo
                    ) {

                        throw new \RuntimeException(
                            'A validated row is missing plot or possession number. Row: '
                            . (
                                $validationRow['row_number']
                                ?? '?'
                            )
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Lock plot
                    |--------------------------------------------------------------------------
                    |
                    | Prevent simultaneous import / creation against same plot.
                    |
                    */

                    $plot =
                        Plot::whereKey($plotId)
                            ->lockForUpdate()
                            ->first();


                    if (!$plot) {

                        throw new \RuntimeException(
                            'Plot ID '
                            . $plotId
                            . ' was not found during import.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate protection
                    |--------------------------------------------------------------------------
                    |
                    | withTrashed() means soft-deleted possession numbers
                    | are also considered reserved.
                    |
                    */

                    $alreadyExists =
                        PossessionCase::withTrashed()
                            ->where(
                                'plot_id',
                                $plot->id
                            )
                            ->where(
                                'possession_no',
                                $possessionNo
                            )
                            ->exists();


                    if ($alreadyExists) {

                        throw new \RuntimeException(
                            "Possession '{$possessionNo}' already exists for Plot '{$plot->plot_number}'."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Create Possession Case
                    |--------------------------------------------------------------------------
                    |
                    | Historical numbering comes directly from validation.
                    | We DO NOT generate a new number here.
                    |
                    */

                    $case =
                        PossessionCase::create([

                            'plot_id' =>
                                $plot->id,

                            'possession_no' =>
                                $possessionNo,

                            'reference_no' =>
                                $validationRow[
                                    'reference_no'
                                ]
                                ?? null,

                            'possession_sequence' =>
                                $validationRow[
                                    'possession_sequence'
                                ],

                            'revision_no' =>
                                $validationRow[
                                    'revision_no'
                                ],

                            'need_approval' =>
                                $validationRow[
                                    'need_approval'
                                ]
                                ?? false,

                            /*
                            | Historical possession is already completed.
                            */
                            'current_status' =>
                                'completed',

                            'current_holder_type' =>
                                null,

                            'current_holder_id' =>
                                null,

                            'current_holder_name' =>
                                null,

                            'received_at' =>
                                $validationRow[
                                    'received_at'
                                ]
                                ?? null,

                            /*
                            | Historical completion / handover date.
                            */
                            'completed_at' =>
                                $validationRow[
                                    'received_at'
                                ]
                                ?? null,

                            'handed_over_at' =>
                                $validationRow[
                                    'received_at'
                                ]
                                ?? null,

                            'remarks' =>
                                'Historical possession imported from '
                                . (
                                    $validationData[
                                        'source_file'
                                    ]
                                    ?? 'CSV/Excel'
                                ),

                            /*
                            | Completed historical cases are inactive.
                            */
                            'is_active' =>
                                false,

                            'created_by' =>
                                Auth::id(),

                            'updated_by' =>
                                Auth::id(),
                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Process Owners
                    |--------------------------------------------------------------------------
                    */

                    $owners =
                        $validationRow['owners']
                        ?? [];


                    foreach ($owners as $ownerData) {

                        $cnic =
                            trim(
                                $ownerData['cnic']
                                ?? ''
                            );


                        $ownerName =
                            trim(
                                $ownerData['owner_name']
                                ?? ''
                            );


                        if (
                            $cnic === '' ||
                            $ownerName === ''
                        ) {

                            throw new \RuntimeException(
                                'A validated owner is missing Name or CNIC. '
                                . 'Import row: '
                                . (
                                    $validationRow[
                                        'row_number'
                                    ]
                                    ?? '?'
                                )
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Find Owner by CNIC
                        |--------------------------------------------------------------------------
                        */

                        $owner =
                            Owner::where(
                                'cnic',
                                $cnic
                            )->first();


                        /*
                        |--------------------------------------------------------------------------
                        | Existing Owner
                        |--------------------------------------------------------------------------
                        */

                        if ($owner) {

                            $existingOwnerCount++;


                            /*
                            |--------------------------------------------------------------------------
                            | Update existing owner
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $ownerAction === 'update'
                            ) {

                                $owner->update([

                                    'owner_name' =>
                                        $ownerName,

                                    'relative_name' =>
                                        $ownerData[
                                            'relative_name'
                                        ]
                                        ?? null,

                                    'address' =>
                                        $ownerData[
                                            'address'
                                        ]
                                        ?? null,

                                    'contact_no' =>
                                        $ownerData[
                                            'contact_no'
                                        ]
                                        ?? null,
                                ]);


                                $updatedOwnerCount++;
                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | New Owner
                        |--------------------------------------------------------------------------
                        */

                        else {

                            $owner =
                                Owner::create([

                                    'owner_name' =>
                                        $ownerName,

                                    'relative_name' =>
                                        $ownerData[
                                            'relative_name'
                                        ]
                                        ?? null,

                                    'cnic' =>
                                        $cnic,

                                    'address' =>
                                        $ownerData[
                                            'address'
                                        ]
                                        ?? null,

                                    'contact_no' =>
                                        $ownerData[
                                            'contact_no'
                                        ]
                                        ?? null,
                                ]);


                            $createdOwnerCount++;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Attach Owner to Possession
                        |--------------------------------------------------------------------------
                        */

                        $case
                            ->owners()
                            ->syncWithoutDetaching([

                                $owner->id => [

                                    'address_snapshot' =>
                                        $ownerData[
                                            'address'
                                        ]
                                        ?? $owner->address,

                                ],

                            ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Imported row count
                    |--------------------------------------------------------------------------
                    */

                    $importedCount++;
                }
            });


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Import failed
            |--------------------------------------------------------------------------
            |
            | DB transaction automatically rolls back.
            |
            */

            report($e);

            return redirect()
                ->route(
                    'possession-cases.import.validation-result'
                )
                ->withErrors([

                    'file' =>
                        'Possession import failed. No records were imported. '
                        . 'Please check the Laravel log for details.',

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Clean temporary validation/session data
        |--------------------------------------------------------------------------
        */

        $sourceFile =
            $validationData[
                'source_file'
            ]
            ?? 'historical file';


        $validationPath =
            $validation['path'];


        Storage::disk('local')->delete(
            $validationPath
        );


        session()->forget([
            'possession_import',
            'possession_import_validation',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Final success message
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'possession-cases.index'
            )
            ->with(
                'success',

                'Historical possession import completed successfully. '
                . $importedCount
                . ' possession record(s) imported. '
                . $createdOwnerCount
                . ' new owner(s) created, '
                . $updatedOwnerCount
                . ' existing owner(s) updated, and '
                . $existingOwnerCount
                . ' existing owner record(s) reused. '
                . 'Source: '
                . $sourceFile
            );
    }
    /**
     * Parse historical possession number.
     *
     * Examples:
     *
     * 300
     * 300-T1
     * 300-T2
     *
     * Result:
     *
     * 300
     *     sequence = 1
     *     revision = 0
     *
     * 300-T1
     *     sequence = 2
     *     revision = 1
     *
     * 300-T2
     *     sequence = 3
     *     revision = 2
     */
    private function parseHistoricalPossessionNumber(
        ?string $value
    ): ?array {

        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Base possession
        |--------------------------------------------------------------------------
        |
        | 300
        |
        */

        if (
            preg_match(
                '/^(\d+)$/',
                $value,
                $matches
            )
        ) {

            return [

                'possession_no' =>
                    $value,

                'base_possession_no' =>
                    $value,

                'possession_sequence' =>
                    1,

                'revision_no' =>
                    0,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Transfer / revision possession
        |--------------------------------------------------------------------------
        |
        | 300-T1
        | 300-T2
        |
        */

        if (
            preg_match(
                '/^(\d+)-T(\d+)$/i',
                $value,
                $matches
            )
        ) {

            $base =
                $matches[1];

            $revision =
                (int) $matches[2];


            return [

                'possession_no' =>
                    $value,

                'base_possession_no' =>
                    $base,

                'possession_sequence' =>
                    $revision + 1,

                'revision_no' =>
                    $revision,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Invalid format
        |--------------------------------------------------------------------------
        */

        return null;
    }
    

    /**
     * Display possession cases.
     */
    public function index(Request $request)
    {
        $query = PossessionCase::with([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
            'creator',
            // test
            'plot.latestAreavariation',
        ])->latest();

        /*
        |--------------------------------------------------------------------------
        | Search by possession number
        |--------------------------------------------------------------------------
        */
        if ($request->filled('possession_no')) {
            $query->where(
                'possession_no',
                'like',
                '%' . $request->possession_no . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search by reference number
        |--------------------------------------------------------------------------
        */
        if ($request->filled('reference_no')) {
            $query->where(
                'reference_no',
                'like',
                '%' . $request->reference_no . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter by status
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $query->where(
                'current_status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter active/inactive
        |--------------------------------------------------------------------------
        */
        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                $request->is_active
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search by owner name
        |--------------------------------------------------------------------------
        */
        if ($request->filled('owner_name')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where(
                    'owner_name',
                    'like',
                    '%' . $request->owner_name . '%'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Search by CNIC
        |--------------------------------------------------------------------------
        */
        if ($request->filled('cnic')) {
            $query->whereHas('owners', function ($q) use ($request) {
                $q->where(
                    'cnic',
                    'like',
                    '%' . $request->cnic . '%'
                );
            });
        }

        $possessionCases = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'possession_cases.index',
            compact('possessionCases')
        );
    }


    /**
     * Show form for creating a new possession case.
     */
    public function create(Request $request)
    {
        $projects = Project::select('id', 'project_name')
            ->orderBy('project_name')
            ->get();

        $propertyTypes = PropertyType::orderBy('name')
            ->get();

        $selectedPlot = null;

        if ($request->filled('plot_id')) {

            $selectedPlot = Plot::with([
                'project',
                'block',
                'street',
                'size',
                'propertyType',
                // test
                'latestAreavariation',
            ])->find($request->plot_id);
        }

        return view('possession_cases.create', compact(
            'projects',
            'propertyTypes',
            'selectedPlot'
        ));
    }

    /**
     * Get blocks according to selected project and property type.
     */
    public function getBlocks(Request $request, $projectId)
    {
        $propertyTypeId = $request->property_type_id;

        $query = Block::where('project_id', $projectId);

        /*
        |--------------------------------------------------------------------------
        | Property Type Selected
        |--------------------------------------------------------------------------
        | Sirf woh blocks show honge jin mein selected
        | property type ke plots mojood hain.
        |--------------------------------------------------------------------------
        */

        if ($propertyTypeId) {

            $blockIds = Plot::where('project_id', $projectId)
                ->where('property_type_id', $propertyTypeId)
                ->pluck('block_id')
                ->unique();

            $query->whereIn('id', $blockIds);
        }

        return $query
            ->orderBy('block_name')
            ->get([
                'id',
                'block_name',
            ]);
    }

    public function getPossessionPreview($plotId)
    {
        $plot = Plot::findOrFail($plotId);

        /*
        |--------------------------------------------------------------------------
        | Find latest possession for this plot
        |--------------------------------------------------------------------------
        | withTrashed() is important because cancelled/deleted old
        | possession numbers must remain reserved.
        |--------------------------------------------------------------------------
        */

        $previousCase = PossessionCase::withTrashed()
            ->where('plot_id', $plot->id)
            ->orderByDesc('possession_sequence')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Next Sequence
        |--------------------------------------------------------------------------
        */

        $nextSequence = ($previousCase?->possession_sequence ?? 0) + 1;

        /*
        |--------------------------------------------------------------------------
        | Base Possession Number
        |--------------------------------------------------------------------------
        */

        if ($previousCase) {

            $basePossessionNo = $this->getBasePossessionNumber(
                $previousCase->possession_no
            );

        } else {

            $basePossessionNo = $this->generateBasePossessionNumber($plot);
        }

        /*
        |--------------------------------------------------------------------------
        | Final Possession Number
        |--------------------------------------------------------------------------
        */

        if ($nextSequence === 1) {

            $possessionNo = $basePossessionNo;

            $caseType = 'Possession';

        } else {

            $possessionNo =
                $basePossessionNo . '-T' . ($nextSequence - 1);

            $caseType = 'Re-Possession';
        }

        return response()->json([
            'possession_no' => $possessionNo,
            'case_type' => $caseType,
            'possession_sequence' => $nextSequence,
        ]);
    }

    /**
     * Get streets according to selected block.
     */
    public function getStreets($blockId)
    {
        $streets = Street::where(
                'block_id',
                $blockId
            )
            ->orderBy('street_name')
            ->get([
                'id',
                'street_name',
            ]);

        return response()->json($streets);
    }


    /**
     * Search plots.
     */
    public function searchPlots(Request $request)
    {
        $request->validate([
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'block_id' => [
                'required',
                'integer',
                'exists:blocks,id',
            ],

            'street_id' => [
                'nullable',
                'integer',
                'exists:streets,id',
            ],

            'plot_number' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $query = Plot::with([
            'project',
            'block',
            'street',
            'size',
            'propertyType',
        ])
            ->where(
                'project_id',
                $request->project_id
            )
            ->where(
                'block_id',
                $request->block_id
            )
            ->where(
                'plot_number',
                $request->plot_number
            );

        /*
        |--------------------------------------------------------------------------
        | Optional Street
        |--------------------------------------------------------------------------
        */
        if ($request->filled('street_id')) {
            $query->where(
                'street_id',
                $request->street_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Soft deleted plots automatically excluded
        |--------------------------------------------------------------------------
        */
        $plots = $query
            ->orderBy('plot_number')
            ->limit(50)
            ->get();

        $results = $plots->map(function ($plot) {

            /*
            |--------------------------------------------------------------------------
            | Property Type Name
            |--------------------------------------------------------------------------
            | Agar PropertyType model mein name hai to name.
            | Agar title hai to title.
            */
            $propertyTypeName =
                $plot->propertyType?->name
                ?? $plot->propertyType?->title
                ?? $plot->propertyType?->property_type
                ?? '-';

            return [
                'id' => $plot->id,

                'plot_number' =>
                    $plot->plot_number,

                'project_name' =>
                    $plot->project?->project_name,

                'block_name' =>
                    $plot->block?->block_name,

                'street_name' =>
                    $plot->street?->street_name,

                'size_title' =>
                    $plot->size?->title,

                'size_area' =>
                    $plot->size?->size_area,

                'property_type_name' =>
                    $propertyTypeName,
            ];
        });

        return response()->json($results);
    }


    /**
     * Generate next base possession number.
     *
     * Numbering project + property type ke hisaab se hogi.
     *
     * Example:
     * 250
     * 251
     * 252
     */

    private function generateBasePossessionNumber(Plot $plot): string
    {
        /*
        |--------------------------------------------------------------------------
        | Lock Project
        |--------------------------------------------------------------------------
        |
        | Same Project + Property Type mein agar 2 different plots par
        | simultaneously new possession create ho rahi ho,
        | to dono ko same MAX number milne se prevent karta hai.
        |
        */
        Project::whereKey($plot->project_id)
            ->lockForUpdate()
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Find Existing Base Possession Numbers
        |--------------------------------------------------------------------------
        |
        | withTrashed() is liye use ho raha hai taake soft-deleted
        | possession cases ke numbers bhi dobara reuse na hon.
        |
        */
        $query = PossessionCase::withTrashed()
            ->whereNotNull('possession_no')
            ->whereRaw(
                "possession_no REGEXP '^[0-9]+$'"
            )
            ->whereHas('plot', function ($q) use ($plot) {

                /*
                | Soft-deleted plot ke possession records bhi
                | numbering mein count honge.
                */
                $q->withTrashed()
                    ->where(
                        'project_id',
                        $plot->project_id
                    );

                /*
                | Property Type ke hisaab se separate numbering.
                */
                if ($plot->property_type_id !== null) {

                    $q->where(
                        'property_type_id',
                        $plot->property_type_id
                    );

                } else {

                    $q->whereNull(
                        'property_type_id'
                    );
                }
            });


        /*
        |--------------------------------------------------------------------------
        | Get Highest Existing Base Number
        |--------------------------------------------------------------------------
        |
        | Example:
        | 1, 2, 3, 7, 9
        |
        | MAX = 9
        | Next = 10
        |
        | Gaps reuse nahi honge.
        |
        */
        $maxNumber = $query->max(
            DB::raw(
                'CAST(possession_no AS UNSIGNED)'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Next Number
        |--------------------------------------------------------------------------
        */
        return (string) (
            ((int) $maxNumber) + 1
        );
    }


    /**
     * Extract base possession number.
     *
     * 250       -> 250
     * 250-T1    -> 250
     * 250-T2    -> 250
     */
    private function getBasePossessionNumber(
        ?string $possessionNo
    ): ?string {

        if (!$possessionNo) {

            return null;

        }

        return preg_replace(
            '/-T\d+$/i',
            '',
            trim($possessionNo)
        );
    }



    /**
     * Store a new possession case.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Plot
            |--------------------------------------------------------------------------
            */
            'plot_id' => [
                'required',
                'integer',
                Rule::exists('plots', 'id')
                    ->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Reference Number
            |--------------------------------------------------------------------------
            */
            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */
            'need_approval' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Current Holder
            |--------------------------------------------------------------------------
            */
            'current_holder_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'current_holder_id' => [
                'nullable',
                'integer',
            ],

            'current_holder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Received Date
            |--------------------------------------------------------------------------
            */
            'received_at' => [
                'nullable',
                'date',
            ],

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */
            'remarks' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Owners
            |--------------------------------------------------------------------------
            */
            'owners' => [
                'required',
                'array',
                'min:1',
            ],

            'owners.*.owner_id' => [
                'nullable',
                'integer',
                'exists:owners,id',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.relative_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                'required',
                'string',
                'max:30',
            ],

            'owners.*.address' => [
                'nullable',
                'string',
            ],

            'owners.*.contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */
        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Lock Plot
            |--------------------------------------------------------------------------
            |
            | Same plot par agar simultaneously possession create ho
            | to sequence control mein rahe.
            |
            */
            $plot = Plot::whereKey(
                $validated['plot_id']
            )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Find Previous Possession
            |--------------------------------------------------------------------------
            */

            // soft delete k sath wala code is mn soft delete ka possession no b reserve he ho ga resue nae hoga 
            // yani previouse possession find krty howay softdelete kia howa possession b find hoga
            $previousCase = PossessionCase::withTrashed()
                ->where(
                    'plot_id',
                    $plot->id
                )
                ->orderBy(
                    'possession_sequence',
                    'desc'
                )
                ->lockForUpdate()
                ->first();

            $nextSequence =
                // soft delete ko b sath mn find kryga awr uska number b dekhy ga
                ((int) PossessionCase::withTrashed()
                    ->where(
                // soft delete ko find nae krny ka code 
                // ((int) PossessionCase::where(
                    'plot_id',
                    $plot->id
                )->max('possession_sequence')) + 1;


            /*
            |--------------------------------------------------------------------------
            | Base Possession Number
            |--------------------------------------------------------------------------
            */
            $basePossessionNo = null;

            if ($previousCase) {
                $basePossessionNo =
                    $this->getBasePossessionNumber(
                        $previousCase->possession_no
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Agar previous case ka base number available nahi
            |--------------------------------------------------------------------------
            */
            if (
                !$basePossessionNo ||
                !ctype_digit($basePossessionNo)
            ) {
                $basePossessionNo =
                    $this->generateBasePossessionNumber(
                        $plot
                    );
            }


            if ($nextSequence === 1) {

                $possessionNo =
                    $basePossessionNo;

            } else {

                $possessionNo =
                    $basePossessionNo
                    . '-T'
                    . ($nextSequence - 1);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Possession Case
            |--------------------------------------------------------------------------
            */
            $case = PossessionCase::create([

                'plot_id' =>
                    $plot->id,

                'possession_no' =>
                    $possessionNo,

                'reference_no' =>
                    $validated['reference_no']
                    ?? null,

                'possession_sequence' =>
                    $nextSequence,

                /*
                | Initial creation always revision 0.
                */
                'revision_no' =>
                    0,

                'need_approval' =>
                    $validated['need_approval']
                    ?? false,

                'current_status' =>
                    'received',

                'current_holder_type' =>
                    $validated['current_holder_type']
                    ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id']
                    ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name']
                    ?? null,

                'received_at' =>
                    $validated['received_at']
                    ?? now()->toDateString(),

                'remarks' =>
                    $validated['remarks']
                    ?? null,

                'is_active' =>
                    true,

                'created_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Save Owners
            |--------------------------------------------------------------------------
            */
            foreach (
                $validated['owners']
                as $ownerData
            ) {

                $owner = null;


                /*
                |--------------------------------------------------------------------------
                | Owner ID
                |--------------------------------------------------------------------------
                */
                if (!empty($ownerData['owner_id'])) {

                    $owner = Owner::find(
                        $ownerData['owner_id']
                    );

                    if (!$owner) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                'Selected owner record was not found.',
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CNIC
                |--------------------------------------------------------------------------
                */
                $cnic = trim(
                    $ownerData['cnic']
                );


                /*
                |--------------------------------------------------------------------------
                | Search owner by CNIC
                |--------------------------------------------------------------------------
                */
                $ownerByCnic = Owner::where(
                    'cnic',
                    $cnic
                )->first();


                /*
                |--------------------------------------------------------------------------
                | Existing CNIC
                |--------------------------------------------------------------------------
                */
                if ($ownerByCnic) {

                    /*
                    | Selected owner ID different hai
                    */
                    if (
                        $owner &&
                        $owner->id !== $ownerByCnic->id
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "CNIC {$cnic} is already registered with another owner: {$ownerByCnic->owner_name}. Please verify the CNIC.",
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Name safety check
                    |--------------------------------------------------------------------------
                    */
                    $enteredName =
                        trim(
                            $ownerData['owner_name']
                        );

                    $existingName =
                        trim(
                            $ownerByCnic->owner_name
                        );

                    if (
                        strcasecmp(
                            $enteredName,
                            $existingName
                        ) !== 0
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "This CNIC is already registered with the name '{$existingName}'. Please verify the CNIC and owner name.",
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Existing owner use karein
                    |--------------------------------------------------------------------------
                    */
                    $owner =
                        $ownerByCnic;
                }


                /*
                |--------------------------------------------------------------------------
                | New CNIC
                |--------------------------------------------------------------------------
                */
                else {

                    if ($owner) {

                        /*
                        |--------------------------------------------------------------------------
                        | Existing selected owner
                        |--------------------------------------------------------------------------
                        */
                        $owner->update([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Completely new owner
                        |--------------------------------------------------------------------------
                        */
                        $owner = Owner::create([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Attach Owner
                |--------------------------------------------------------------------------
                */
                $case->owners()->attach(
                    $owner->id,
                    [
                        'address_snapshot' =>
                            $ownerData['address']
                            ?? $owner->address,
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | First History Record
            |--------------------------------------------------------------------------
            */
            $case->histories()->create([

                'plot_id' =>
                    $case->plot_id,

                'action' =>
                    'Case Received',

                'old_status' =>
                    null,

                'new_status' =>
                    'received',

                'old_holder' =>
                    null,

                'new_holder' =>
                    $case->current_holder_name,

                'handed_over_to' =>
                    null,

                'remarks' =>
                    'Possession case created. Possession No: '
                    . $case->possession_no,

                'user_id' =>
                    Auth::id(),
            ]);
        });


        return redirect()
            ->route('possession-cases.index')
            ->with(
                'success',
                'Possession case created successfully.'
            );
    }


    /**
     * Display a specific possession case.
     */
    public function show(
        PossessionCase $possessionCase
    ) {
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
            'histories.user',
            'creator',
            'updater',
        ]);

        return view(
            'possession_cases.show',
            compact('possessionCase')
        );
    }


    /**
     * Show edit form.
     */
    public function edit(
        PossessionCase $possessionCase
    ) {
        $possessionCase->load([
            'plot.project',
            'plot.block',
            'plot.street',
            'plot.size',
            'plot.propertyType',
            'owners',
        ]);

        $projects = Project::orderBy(
                'project_name'
            )
            ->get([
                'id',
                'project_name',
            ]);

        return view(
            'possession_cases.edit',
            compact(
                'possessionCase',
                'projects'
            )
        );
    }


    /**
     * Update possession case.
     */
    public function update(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([

            'plot_id' => [
                'required',
                'integer',
                Rule::exists('plots', 'id')
                    ->where(function ($query) {
                        $query->whereNull('deleted_at');
                    }),
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:255',
            ],

            'need_approval' => [
                'nullable',
                'boolean',
            ],

            'current_holder_type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'current_holder_id' => [
                'nullable',
                'integer',
            ],

            'current_holder_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'received_at' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Owners
            |--------------------------------------------------------------------------
            */
            'owners' => [
                'required',
                'array',
                'min:1',
            ],

            'owners.*.id' => [
                'nullable',
                'integer',
            ],

            'owners.*.owner_id' => [
                'nullable',
                'integer',
                'exists:owners,id',
            ],

            'owners.*.owner_name' => [
                'required',
                'string',
                'max:255',
            ],

            'owners.*.relative_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'owners.*.cnic' => [
                'required',
                'string',
                'max:30',
            ],

            'owners.*.address' => [
                'nullable',
                'string',
            ],

            'owners.*.contact_no' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Plot change prevent
        |--------------------------------------------------------------------------
        |
        | Possession number plot ke sath linked hai.
        | Is liye existing case ko doosre plot par move nahi karenge.
        */
        if (
            (int) $validated['plot_id']
            !== (int) $possessionCase->plot_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'plot_id' =>
                        'An existing possession case cannot be moved to another plot.',
                ]);
        }


        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update Possession Case
            |--------------------------------------------------------------------------
            */
            $possessionCase->update([

                'reference_no' =>
                    $validated['reference_no']
                    ?? null,

                'need_approval' =>
                    $validated['need_approval']
                    ?? false,

                'current_holder_type' =>
                    $validated['current_holder_type']
                    ?? null,

                'current_holder_id' =>
                    $validated['current_holder_id']
                    ?? null,

                'current_holder_name' =>
                    $validated['current_holder_name']
                    ?? null,

                'received_at' =>
                    $validated['received_at']
                    ?? null,

                'remarks' =>
                    $validated['remarks']
                    ?? null,

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Existing owners
            |--------------------------------------------------------------------------
            */
            $oldOwnerIds =
                $possessionCase
                    ->owners()
                    ->pluck('owners.id')
                    ->toArray();


            $existingOwnerIds = [];


            /*
            |--------------------------------------------------------------------------
            | Process Owners
            |--------------------------------------------------------------------------
            */
            foreach (
                $validated['owners']
                as $ownerData
            ) {

                $owner = null;


                $ownerId =
                    $ownerData['owner_id']
                    ?? $ownerData['id']
                    ?? null;


                if ($ownerId) {

                    $owner = Owner::find(
                        $ownerId
                    );

                    if (!$owner) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                'Selected owner record was not found.',
                        ]);
                    }
                }


                $cnic =
                    trim(
                        $ownerData['cnic']
                    );


                $ownerByCnic =
                    Owner::where(
                        'cnic',
                        $cnic
                    )->first();


                /*
                |--------------------------------------------------------------------------
                | Existing CNIC
                |--------------------------------------------------------------------------
                */
                if ($ownerByCnic) {

                    if (
                        $owner &&
                        $owner->id
                            !== $ownerByCnic->id
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "CNIC {$cnic} is already registered with another owner: {$ownerByCnic->owner_name}. Please verify the CNIC.",
                        ]);
                    }


                    $enteredName =
                        trim(
                            $ownerData['owner_name']
                        );

                    $existingName =
                        trim(
                            $ownerByCnic->owner_name
                        );


                    if (
                        strcasecmp(
                            $enteredName,
                            $existingName
                        ) !== 0
                    ) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'owners' =>
                                "This CNIC is already registered with the name '{$existingName}'. Please verify the CNIC and owner name.",
                        ]);
                    }


                    $owner =
                        $ownerByCnic;

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | New CNIC
                    |--------------------------------------------------------------------------
                    */
                    if ($owner) {

                        $owner->update([

                            'owner_name' =>
                                $ownerData['owner_name'],

                            'relative_name' =>
                                $ownerData['relative_name']
                                ?? null,

                            'cnic' =>
                                $cnic,

                            'address' =>
                                $ownerData['address']
                                ?? null,

                            'contact_no' =>
                                $ownerData['contact_no']
                                ?? null,
                        ]);

                    } else {

                        $owner =
                            Owner::create([

                                'owner_name' =>
                                    $ownerData['owner_name'],

                                'relative_name' =>
                                    $ownerData['relative_name']
                                    ?? null,

                                'cnic' =>
                                    $cnic,

                                'address' =>
                                    $ownerData['address']
                                    ?? null,

                                'contact_no' =>
                                    $ownerData['contact_no']
                                    ?? null,
                            ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Attach / update pivot
                |--------------------------------------------------------------------------
                */
                $possessionCase
                    ->owners()
                    ->syncWithoutDetaching([

                        $owner->id => [

                            'address_snapshot' =>
                                $ownerData['address']
                                ?? $owner->address,
                        ],
                    ]);


                $existingOwnerIds[] =
                    $owner->id;
            }


            /*
            |--------------------------------------------------------------------------
            | Detach removed owners
            |--------------------------------------------------------------------------
            */
            $ownersToDetach =
                array_diff(
                    $oldOwnerIds,
                    $existingOwnerIds
                );


            if (!empty($ownersToDetach)) {

                $possessionCase
                    ->owners()
                    ->detach(
                        $ownersToDetach
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Updater
            |--------------------------------------------------------------------------
            */
            $possessionCase->update([
                'updated_by' =>
                    Auth::id(),
            ]);
        });


        return redirect()
            ->route(
                'possession-cases.show',
                $possessionCase
            )
            ->with(
                'success',
                'Possession case updated successfully.'
            );
    }


    /**
     * Update case status.
     */
    public function updateStatus(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([

            'status' => [
                'required',
                'in:received,prepared,surveyor_signed,approval,town_planner_signed,completed',
            ],

            'handed_over_to' => [
                $request->status === 'completed'
                    ? 'required'
                    : 'nullable',

                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Cancelled case cannot continue workflow
        |--------------------------------------------------------------------------
        */
        if (
            $possessionCase->current_status
            === 'cancelled'
        ) {

            return back()
                ->withErrors([
                    'status' =>
                        'Cancelled possession case cannot continue through the normal workflow.',
                ]);
        }


        $currentStatus =
            $possessionCase->current_status;

        $newStatus =
            $validated['status'];


        /*
        |--------------------------------------------------------------------------
        | Allowed Workflow
        |--------------------------------------------------------------------------
        */
        if ($possessionCase->need_approval) {

            $allowedNextStatuses = [

                'received' => [
                    'prepared',
                ],

                'prepared' => [
                    'surveyor_signed',
                ],

                'surveyor_signed' => [
                    'approval',
                ],

                'approval' => [
                    'town_planner_signed',
                ],

                'town_planner_signed' => [
                    'completed',
                ],

                'completed' => [],
            ];

        } else {

            $allowedNextStatuses = [

                'received' => [
                    'prepared',
                ],

                'prepared' => [
                    'surveyor_signed',
                ],

                'surveyor_signed' => [
                    'completed',
                ],

                'completed' => [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Validate transition
        |--------------------------------------------------------------------------
        */
        if (
            !in_array(
                $newStatus,
                $allowedNextStatuses[$currentStatus]
                ?? []
            )
        ) {

            return back()
                ->withErrors([
                    'status' =>
                        'Invalid status transition. Please follow the proper possession workflow.',
                ])
                ->withInput();
        }


        DB::transaction(function () use (
            $validated,
            $possessionCase,
            $currentStatus,
            $newStatus
        ) {

            /*
            |--------------------------------------------------------------------------
            | Holder
            |--------------------------------------------------------------------------
            */
            $oldHolderName =
                $possessionCase
                    ->current_holder_name;

            $newHolderName =
                $validated['handed_over_to']
                ?? null;


            /*
            |--------------------------------------------------------------------------
            | Date Field
            |--------------------------------------------------------------------------
            */
            $dateField = match ($newStatus) {

                'received' =>
                    'received_at',

                'prepared' =>
                    'prepared_at',

                'surveyor_signed' =>
                    'surveyor_signed_at',

                'approval' =>
                    'approval_sent_at',

                'town_planner_signed' =>
                    'town_planner_signed_at',

                'completed' =>
                    'completed_at',

                default =>
                    null,
            };


            /*
            |--------------------------------------------------------------------------
            | Prepare Update Data FIRST
            |--------------------------------------------------------------------------
            */
            $updateData = [

                'current_status' =>
                    $newStatus,

                'updated_by' =>
                    Auth::id(),
            ];


            /*
            |--------------------------------------------------------------------------
            | Save Date
            |--------------------------------------------------------------------------
            */
            if ($dateField) {

                $updateData[$dateField] =
                    now()->toDateString();
            }


            /*
            |--------------------------------------------------------------------------
            | Handover
            |--------------------------------------------------------------------------
            */
            if (!empty($newHolderName)) {

                $updateData[
                    'handed_over_to'
                ] = $newHolderName;

                $updateData[
                    'current_holder_name'
                ] = $newHolderName;
            }


            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */
            if (!empty($validated['remarks'])) {

                $updateData['remarks'] =
                    $validated['remarks'];
            }


            /*
            |--------------------------------------------------------------------------
            | Completed
            |--------------------------------------------------------------------------
            */
            if ($newStatus === 'completed') {

                $updateData['is_active'] =
                    false;

                $updateData['handed_over_at'] =
                    now()->toDateString();
            }


            /*
            |--------------------------------------------------------------------------
            | Update Case
            |--------------------------------------------------------------------------
            */
            $possessionCase->update(
                $updateData
            );


            /*
            |--------------------------------------------------------------------------
            | History
            |--------------------------------------------------------------------------
            */
            $actionLabels = [

                'received' =>
                    'Case Received',

                'prepared' =>
                    'Case Prepared',

                'surveyor_signed' =>
                    'Surveyor Signed',

                'approval' =>
                    'Approval Sent',

                'town_planner_signed' =>
                    'Town Planner Signed',

                'completed' =>
                    'Case Completed',
            ];


            $possessionCase
                ->histories()
                ->create([

                    'plot_id' =>
                        $possessionCase->plot_id,

                    'action' =>
                        $actionLabels[$newStatus]
                        ?? ucfirst(
                            str_replace(
                                '_',
                                ' ',
                                $newStatus
                            )
                        ),

                    'old_status' =>
                        $currentStatus,

                    'new_status' =>
                        $newStatus,

                    'old_holder' =>
                        $oldHolderName,

                    'new_holder' =>
                        $newHolderName
                        ?? $oldHolderName,

                    'handed_over_to' =>
                        $newHolderName,

                    'remarks' =>
                        $validated['remarks']
                        ?? null,

                    'user_id' =>
                        Auth::id(),
                ]);
        });


        return back()
            ->with(
                'success',
                'Possession case status updated successfully.'
            );
    }


    /**
     * Cancel possession case.
     *
     * Cancelled possession delete nahi hoti.
     * Sirf status cancelled aur inactive hota hai.
     */
    public function cancel(
        Request $request,
        PossessionCase $possessionCase
    ) {
        $validated = $request->validate([

            'cancellation_reason' => [
                'required',
                'string',
                'min:3',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Already cancelled
        |--------------------------------------------------------------------------
        */

        if ($possessionCase->current_status === 'cancelled') {

            return back()
                ->withErrors([
                    'cancellation_reason' =>
                        'This possession case is already cancelled.',
                ]);
        }

        if ($possessionCase->current_status !== 'completed') {

            return back()
                ->withErrors([
                    'cancellation_reason' =>
                        'Only completed possession cases can be cancelled.',
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $possessionCase
        ) {

            $oldStatus =
                $possessionCase->current_status;

            $oldHolder =
                $possessionCase->current_holder_name;


            /*
            |--------------------------------------------------------------------------
            | Cancel Case
            |--------------------------------------------------------------------------
            */
            $possessionCase->update([

                'current_status' =>
                    'cancelled',

                'is_active' =>
                    false,

                'cancelled_at' =>
                    now()->toDateString(),

                'cancelled_by' =>
                    Auth::id(),

                'cancellation_reason' =>
                    $validated[
                        'cancellation_reason'
                    ],

                'updated_by' =>
                    Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | History
            |--------------------------------------------------------------------------
            */
            $possessionCase
                ->histories()
                ->create([

                    'plot_id' =>
                        $possessionCase->plot_id,

                    'action' =>
                        'Possession Cancelled',

                    'old_status' =>
                        $oldStatus,

                    'new_status' =>
                        'cancelled',

                    'old_holder' =>
                        $oldHolder,

                    'new_holder' =>
                        $oldHolder,

                    'handed_over_to' =>
                        null,

                    'remarks' =>
                        $validated[
                            'cancellation_reason'
                        ],

                    'user_id' =>
                        Auth::id(),
                ]);
        });


        return back()
            ->with(
                'success',
                'Possession case cancelled successfully.'
            );
    }


    /**
     * Soft delete possession case.
     */
    public function destroy(
        PossessionCase $possessionCase
    ) {
        $possessionCase->update([

            'is_active' =>
                false,

            'updated_by' =>
                Auth::id(),
        ]);

        $possessionCase->delete();

        return redirect()
            ->route(
                'possession-cases.index'
            )
            ->with(
                'success',
                'Possession case deleted successfully.'
            );
    }
}