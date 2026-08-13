<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Plot No</th>
            <th>Street</th>
            <th>Size</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($plots as $index => $plot)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $plot->plot_number }}</td>
                <td>{{ $plot->street->street_name ?? '-' }}</td>
                <td>{{ $plot->plotsize->title ?? '-' }}</td>
                <td>{{ ucfirst($plot->status ?? 'active') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
