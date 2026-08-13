<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Block Name</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($blocks as $index => $block)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $block->block_name }}</td>
                <td>{{ $block->remarks ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
