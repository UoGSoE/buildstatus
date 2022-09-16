<div>
    <div class="level">
        <div class="level-left">
            <div class="level-item">
                <h1 class="title">Machines</h1>
            </div>
            <div class="level-item">
                <div class="field has-addons">
                    <div class="control">
                        <input class="input" type="text" wire:model.defer="search">
                    </div>
                    <div class="control">
                        <button class="button" wire:click.prevent="search">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="level-right">
            <div class="level-item">
                <div class="field has-addons">
                    <div class="control">
                        <input class="input" type="password" wire:model.defer="password" placeholder="Secret password...">
                    </div>
                    <div class="control">
                        <button class="button" wire:click.prevent="truncateMachines">Wipe Everything</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table is-fullwidth is-striped is-hoverable" wire:poll.30s>
        <thead>
            <tr>
                <th>Name</th>
                <th>IP</th>
                <th>Status</th>
                <th>Started</th>
                <th>Updated</th>
                <th>Finished</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($machines as $machine)
                <tr wire:key="machine-row-{{ $machine->id }}">
                    <td>{{ $machine->name }}</td>
                    <td>{{ $machine->ip_address }}</td>
                    <td>{{ $machine->status }}</td>
                    <td>{{ $machine->started_at?->format('d/m/Y H:i') }}</td>
                    <td title="{{ $machine->updated_at->format('d/m/Y H:i') }}">{{ $machine->updated_at->diffForHumans() }}</td>
                    <td>{{ $machine->finished_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <button class="button is-small is-primary is-outlined" wire:click="markComplete({{ $machine->id }})">Complete</button>
                        <button class="button is-small is-danger is-outlined" wire:click="deleteMachine({{ $machine->id }})">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
