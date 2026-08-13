<!DOCTYPE html>
<html>
<head>
    <title>Area Variation Report - {{ $plot->plot_no }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            font-size: 14px;
        }
        h2 {
            margin-bottom: 0;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }
        @media print {
            button { display: none; }
        }
    </style>
</head>

<body>

<button onclick="window.print()">Print</button>

<h2>Area Variation Report</h2>
<div class="subtitle">
    Plot No: <b>{{ $plot->plot_no }}</b> |
    Size: <b>{{ $plot->size }} sqft</b> |
    Project: <b>{{ $plot->project->name }}</b>
</div>

<table>
    <tr>
        <th>#</th>
        <th>Measured Area</th>
        <th>Difference</th>
        <th>Measured By</th>
        <th>Measured Date</th>
        <th>Remarks</th>
    </tr>

    @foreach($variations as $v)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $v->measured_area }} sqft</td>
        <td>
            {{ number_format($v->measured_area - $plot->size, 2) }} sqft
        </td>
        <td>{{ $v->measured_by }}</td>
        <td>{{ $v->measured_date }}</td>
        <td>{{ $v->remarks }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
