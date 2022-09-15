<div>
    <table class="table is-fullwidth is-striped is-hoverable" wire:poll.30s>
        <thead>
            <tr>
                <th>Name</th>
                <th>IP</th>
                <th>Status</th>
                <th>Started</th>
                <th>Updated</th>
                <th>Finished</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($machines as $machine)
                <tr wire:key="machine-row-{{ $machine->id }}">
                    <td>{{ $machine->name }}</td>
                    <td>{{ $machine->ip_address }}</td>
                    <td>{{ $machine->status }}</td>
                    <td>{{ $machine->started_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $machine->updated_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $machine->finished_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
