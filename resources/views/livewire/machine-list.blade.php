<div>
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <flux:input wire:model.live="filter" placeholder="Filter" />
            <flux:select variant="listbox" searchable placeholder="Choose labs..." wire:model.live="labId">
                <flux:select.option value="">All labs</flux:select.option>
                @foreach ($labs as $lab)
                    <flux:select.option value="{{ $lab->id }}">{{ $lab->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <flux:switch wire:model.live="autoRefresh" label="Auto refresh?" />
    </div>
    <flux:separator class="my-2" />
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6" @if ($autoRefresh) wire:poll.6s @endif>
        @foreach ($machines as $machine)
            <flux:card
            wire:key="machine-{{ $machine->id }}"
            size="sm"
            x-data="{ }"
            x-transition.opacity
            >
                <flux:heading>{{ $machine->short_hostname }}</flux:heading>
                <flux:text>{{ $machine->lab->name }}</flux:text>
                <flux:separator class="my-2" />
                <div class="flex items-center text-center gap-2">
                    <flux:badge>{{ $machine->status }}</flux:badge>
                </div>
            </flux:card>
        @endforeach
    </div>
    <div class="mt-4">
        <flux:pagination :paginator="$machines" />
    </div>
</div>
