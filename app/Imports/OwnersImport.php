<?php

namespace App\Imports;

use App\Models\Owner;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OwnersImport implements ToModel, WithHeadingRow
{
    /**
     * Import each CSV row.
     */
    public function model(array $row)
    {
        $cnic = trim($row['cnic'] ?? '');

        // Empty CNIC wali row ko skip kar dein
        if ($cnic === '') {
            return null;
        }

        return Owner::updateOrCreate(
            [
                'cnic' => $cnic,
            ],
            [
                'owner_name'    => trim($row['name'] ?? ''),
                'relative_name' => trim($row['relative_name'] ?? ''),
                'address'       => trim($row['address'] ?? ''),
            ]
        );
    }
}
