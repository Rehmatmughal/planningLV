<table>
<tr>
    <td colspan="4" style="font-size:18px;font-weight:bold;">
        COMPANY NAME
    </td>
</tr>
 
<tr>
<td>Project</td>
<td>{{ $plot->project->project_name }}</td>
<td>Block</td>
<td>{{ $plot->block->block_name }}</td>
</tr>

<tr>
<td>Street</td>
<td>{{ $plot->street->street_name }}</td>
<td>Plot No</td>
<td>{{ $plot->plot_number }}</td>
</tr>

<tr>
<td>Size</td>
<td>{{ $plot->size->title }}</td>
<td>LOP Status</td>
{{-- <td>{{ $plot->lopStatus->lop_status ?? 'N/A' }}</td> --}}
<td>{{ $av->lop_status_at_time ?? 'N/A' }}</td>
</tr>

<tr>
<td>Mortgage</td>
<td>{{ $plot->mortgageStatus->is_mortgaged ?? 'N/A' }}</td>
<td>Sewer</td>
<td>{{ $plot->developmentStatus->sewer_manholes ?? 'N/A' }}</td>
</tr>

<tr>
<td>Asphalt</td>
<td>{{ $plot->developmentStatus->asphalt_tst ?? 'N/A' }}</td>
</tr>

<tr><td colspan="4"></td></tr>

<tr>
<td>Variation Date</td>
<td>{{ $av->measured_date }}</td>
<td>Measured Area</td>
<td>{{ $av->measured_area }}</td>
</tr>

<tr>
<td>Road Status</td>
<td>{{ $av->road_status_at_time }}</td>
<td>Sewer Status</td>
<td>{{ $av->sewer_status_at_time }}</td>
</tr>

<tr>
<td>Remarks</td>
<td colspan="3">{{ $av->remarks }}</td>
</tr>

<tr><td colspan="4"></td></tr>

<tr>
<td style="border-top:1px solid #000;">Prepared By</td>
<td></td>
<td style="border-top:1px solid #000;">Checked By</td>
<td></td>
</tr>

</table>
