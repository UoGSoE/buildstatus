<div class="bg-white p-4 shadow">
    <header class="bg-white shadow">
        <x-layout.level>
            <x-layout.level-section>
                <h1 class="justify-self-start text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>

                <x-form.text wire:model.defer="search" with-button="true">
                    <x-slot:button class="blah-de-blah" wire:click="search">
                        Search
                    </x-slot>
                </x-form.text>

                <div class="">
                    <x-button.plain wire:click.prevent="$toggle('showTagList')" class="flex justify-between items-center">
                        <span>Tags ...</span>
                    </x-button.plain>
                </div>
            </x-layout.level-section>
            <x-layout.level-section>
                <x-form.text wire:model.defer="password" with-button="true">
                    <x-slot:button class="blah-de-blah" wire:click="truncateMachines">
                        Wipe
                    </x-slot>
                </x-form.text>
            </x-layout.level-section>
        </x-layout.level>

        @if ($showTagList)
            <div class="grid grid-cols-4 gap-6 items-center justify-items-start pb-4">
                @foreach ($availableTags as $tag)
                    <div class="relative flex gap-x-3 ml-4">
                        <div class="flex h-6 items-center">
                            <input wire:model="tags" value="{{ $tag->id }}" id="tag-checkbox-{{ $tag->id }}" name="tag-checkbox-{{ $tag->id }}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-600">
                        </div>
                        <div class="text-sm leading-6">
                            <label for="tag-checkbox-{{ $tag->id }}" class="font-medium text-gray-900">{{ $tag->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </header>


    <x-table wire:poll.30s=''>
        <x-table.header>
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>IP</x-table.th>
                <x-table.th>Status</x-table.th>
                <x-table.th>Started</x-table.th>
                <x-table.th>Updated</x-table.th>
                <x-table.th>Finished</x-table.th>
                <x-table.th>Actions</x-table.th>
            <tr>
        </x-table.header>
        <x-table.body>
            @foreach ($machines as $machine)
                <tr wire:key="machine-row-{{ $machine->id }}" id="machine-row-{{ $machine->id }}" @class([
                    'bg-gray-50' => $loop->even,
                    'transition duration-300 ease-in-out hover:bg-neutral-100',
                ])>
                    <x-table.td>{{ $machine->name }}</x-table.td>
                    <x-table.td>{{ $machine->ip_address }}</x-table.td>
                    <x-table.td>{{ $machine->status }}</x-table.td>
                    <x-table.td>{{ $machine->started_at?->format('d/m/Y H:i') }}</x-table.td>
                    <x-table.td title="{{ $machine->updated_at->format('d/m/Y H:i') }}">{{ $machine->updated_at->diffForHumans() }}</x-table.td>
                    <x-table.td>{{ $machine->finished_at?->format('d/m/Y H:i') }}</x-table.td>
                    <x-table.td>
                        <x-button.plain wire:click="markComplete({{ $machine->id }})">Complete</x-button.plain>
                        <x-button.danger wire:click="deleteMachine({{ $machine->id }})">Delete</x-button.danger>
                    </x-table.td>
                </tr>
            @endforeach
        </x-table.body>
    </x-table>


</div>
