<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Area Variation - {{ $av->plot->plot_number }}</title>
  <style>
    body{font-family: sans-serif; padding:20px}
    table{width:100%; border-collapse:collapse}
    th,td{border:1px solid #000; padding:8px; text-align:left}
    @media print { button{display:none} }
  </style>
</head>
<body>
<button onclick="window.print()">Print</button>

<h2>Area Variation - Plot {{ $av->plot->plot_number }}</h2>
<p>
  <strong>Project:</strong> {{ $av->plot->project->project_name ?? '-' }} |
  <strong>Block:</strong> {{ $av->plot->block->block_name ?? '-' }} |
  <strong>Street:</strong> {{ $av->plot->street->street_name ?? '-' }}
</p>

<table>
  <tr><th>Nominal Area</th><td>{{ $av->plot->size ?? '-' }}</td></tr>
  <tr><th>Measured Area</th><td>{{ $av->measured_area }}</td></tr>
  {{-- <tr><th>Difference</th><td>{{ number_format($av->measured_area - ($av->plot->size ?? 0), 2) }}</td></tr> --}}
  <tr><th>Measured By</th><td>{{ $av->measured_by ?? '-' }}</td></tr>
  <tr><th>Measured Date</th><td>{{ $av->measured_date ?? $av->created_at->format('d-M-Y') }}</td></tr>
  <tr><th>Remarks</th><td>{{ $av->remarks ?? '-' }}</td></tr>
  <tr><th>LOP</th><td>{{ $av->plot->lopStatus->lop_status ?? '-' }}</td></tr>
  <tr><th>SewerManhole</th><td>{{ $av->plot->developmentStatus->sewer_manholes ?? '-' }}</td></tr>
  <tr><th>Asphalt / Tst</th><td>{{ $av->plot->developmentStatus->asphalt_tst ?? '-' }}</td></tr>
  <tr><th>Overall status</th><td>{{ $av->plot->developmentStatus->overall_status ?? '-' }}</td></tr>
  {{-- <tr><th>Development status</th><td>{{ $av->plot->development_statuses->sewer_manholes ?? '-' }}</td></tr>
  <tr><th>Development status</th><td>{{ $av->plot->development_statuses->sewer_manholes ?? '-' }}</td></tr> --}}
</table>

</body>
</html>
