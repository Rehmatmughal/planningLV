<!DOCTYPE html>
<html>
<head>
    <title>Plot Detail – Print</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
        }

        @page {
            size: A4;
            margin: 20mm;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0 5px;
        }

        .no-print {
            display: none;
        }

        @media screen {
            .no-print {
                display: block;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨 Print</button>
</div>

<div class="header">
    <h2>Plot Detail Report</h2>
    <p>{{ $plot->project->project_name ?? '-' }}</p>
</div>

{{-- Basic Info --}}
<div class="section-title">Basic Information</div>
<table>
    <tr>
        <th>Block</th>
        <td>{{ $plot->block->block_name ?? '-' }}</td>
        <th>Plot No</th>
        <td>{{ $plot->plot_number }}</td>
    </tr>
    <tr>
        <th>Street</th>
        <td>{{ $plot->street->street_name ?? '-' }}</td>
        <th>Size</th>
        <td>{{ $plot->size->title ?? '-' }}</td>
    </tr>
    <tr>
        <th>Category</th>
        <td>{{ $plot->category->category_title ?? '-' }}</td>
        <th>LOP</th>
        <td>{{ strtoupper($plot->lopStatus->lop_status ?? '-') }}</td>
    </tr>
</table>

{{-- Development --}}
<div class="section-title">Development Status</div>
<table>
    <tr>
        <th>Road</th>
        <td>{{ $plot->developmentStatus->asphalt_tst ?? 'Not Updated' }}</td>
        <th>Sewerage</th>
        <td>{{ $plot->developmentStatus->sewer_manholes ?? 'Not Updated' }}</td>
    </tr>
</table>

{{-- Area Variations --}}
<div class="section-title">Area Variation History</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Measured Area</th>
            <th>Measured By</th>
            <th>Date</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($plot->areaVariations as $i => $area)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ number_format($area->measured_area, 2) }}</td>
                <td>{{ $area->measured_by }}</td>
                <td>{{ $area->measured_date }}</td>
                <td>{{ $area->remarks }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center">No area variation found</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
