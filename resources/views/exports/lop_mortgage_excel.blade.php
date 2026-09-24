<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Project</th>
            <th>Block</th>
            <th>Property Type</th>
            <th>Street</th>
            <th>Size</th>
            <th>Plot No</th>
            <th>LOP</th>
            <th>Mortgage</th>
            <th>Remarks</th>
        </tr>
    </thead>

    <tbody>

        @foreach($plots as $index => $plot)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $plot->project?->project_name ?? '-' }}
                </td>

                <td>
                    {{ $plot->block?->block_name ?? '-' }}
                </td>

                <td>
                    {{ $plot->propertyType?->name ?? '-' }}
                </td>

                <td>
                    {{ $plot->street?->street_name ?? '-' }}
                </td>

                <td>
                    {{ $plot->size?->size ?? '-' }}
                </td>

                <td>
                    {{ $plot->plot_number }}
                </td>

                <td>
                    {{ $plot->lopStatus?->lop_status === 'lop'
                        ? 'LOP'
                        : ($plot->lopStatus?->lop_status === 'non_lop'
                            ? 'Non-LOP'
                            : 'Not Set') }}
                </td>

                <td>
                    {{ $plot->mortgageStatus?->is_mortgaged === 'yes'
                        ? 'Yes'
                        : ($plot->mortgageStatus?->is_mortgaged === 'no'
                            ? 'No'
                            : 'Not Set') }}
                </td>

                <td>
                    {{ $plot->lopStatus?->remarks
                        ?? $plot->mortgageStatus?->remarks
                        ?? '-' }}
                </td>

            </tr>

        @endforeach

    </tbody>
</table>
