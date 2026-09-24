<table>
    <tr>
        <td colspan="11">
            AS BUILT STATUS OF PLOTS
        </td>
    </tr>

    <tr>
        <td colspan="11">
            {{ $projectTitle }}
        </td>
    </tr>

    <tr>
        <td colspan="11">
            Generated on: {{ now()->format('d-M-Y h:i A') }}
        </td>
    </tr>

    <tr>
        <td colspan="11">
            @if(count($appliedFilters) > 0)
                Applied Filters:
                {{ implode(' | ', $appliedFilters) }}
            @else
                Applied Filters: None — All Records
            @endif
        </td>
    </tr>

    <tr>
        <td colspan="11"></td>
    </tr>

    <thead>
        <tr>
            <th>S.No</th>
            <th>Project</th>
            <th>Block</th>
            <th>Property Type</th>
            <th>Street</th>
            <th>Size</th>
            <th>Plot No</th>
            <th>Sewerage / Manholes</th>
            <th>Asphalt / TST</th>
            <th>Overall Status</th>
            <th>Remarks</th>
        </tr>
    </thead>

    <tbody>
        @foreach($plots as $index => $plot)
            <tr>
                <td>{{ $index + 1 }}</td>

                <td>
                    {{ $plot->project?->project_name ?? 'N/A' }}
                </td>

                <td>
                    {{ $plot->block?->block_name ?? 'N/A' }}
                </td>

                <td>
                    {{ $plot->propertyType?->name ?? 'N/A' }}
                </td>

                <td>
                    {{ $plot->street?->street_name ?? 'N/A' }}
                </td>

                <td>
                    {{ $plot->size?->title ?? 'N/A' }}
                </td>

                <td>
                    {{ $plot->plot_number ?? 'N/A' }}
                </td>

                <td>
                    @if($plot->developmentStatus)
                        {{ $plot->developmentStatus->sewer_manholes === 'constructed'
                            ? 'Constructed'
                            : 'Not Constructed' }}
                    @else
                        Not Set
                    @endif
                </td>

                <td>
                    @if($plot->developmentStatus)
                        {{ $plot->developmentStatus->asphalt_tst === 'yes'
                            ? 'Yes'
                            : 'No' }}
                    @else
                        Not Set
                    @endif
                </td>

                <td>
                    @if($plot->developmentStatus)
                        @switch($plot->developmentStatus->overall_status)

                            @case('developed')
                                Developed
                                @break

                            @case('under_development')
                                Under Development
                                @break

                            @case('not_developed')
                                Not Developed
                                @break

                            @default
                                Not Set

                        @endswitch
                    @else
                        Not Set
                    @endif
                </td>

                <td>
                    {{ $plot->developmentStatus?->remarks ?? '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
