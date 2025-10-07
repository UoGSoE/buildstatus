<div>
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <flux:input wire:model.live="filter" clearable placeholder="Filter" />
            <flux:select variant="listbox" searchable placeholder="Choose labs..." wire:model.live="labId">
                <flux:select.option value="">All labs</flux:select.option>
                @foreach ($labs as $lab)
                    <flux:select.option value="{{ $lab->id }}">{{ $lab->name }}</flux:select.option>
                @endforeach
            </flux:select>
            @if(auth()->user()->isAdmin())
                <flux:button variant="danger" icon="trash" wire:click="confirmBulkDelete">
                    Clear Filtered Machines
                </flux:button>
            @endif
        </div>
        <flux:switch wire:model.live="autoRefresh" label="Auto refresh?" />
    </div>
    <flux:separator class="my-2" />
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6"
        @if ($autoRefresh) wire:poll.6s @endif>
        @foreach ($machines as $machine)
            <flux:card
                wire:key="machine-{{ $machine->id }}"
                size="sm"
                class="cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-600"
                title="Last updated: {{ $machine->updated_at->format('d/m/Y H:i:s') }}"
                wire:click="showMachineDetails({{ $machine->id }})"
            >
                <div class="flex flex-col gap-2">
                    <flux:heading class="flex items-center gap-2"><flux:icon.computer-desktop />{{ $machine->short_hostname }}</flux:heading>
                    <flux:text class="flex items-center gap-2"><flux:icon.academic-cap />{{ $machine->lab->name }}</flux:text>
                    <flux:text class="flex items-center gap-2"><flux:icon.clock />{{ $machine->updated_at->diffForHumans() }}</flux:text>
                </div>
                <flux:separator class="my-2" />
                <flux:badge class="w-full">{{ ucfirst($machine->status) }}</flux:badge>
            </flux:card>
        @endforeach
    </div>
    <div class="mt-4">
        <flux:pagination :paginator="$machines" />
    </div>

    <flux:modal name="machine-details" variant="flyout">
        <div class="space-y-6">
            <div class="flex flex-row items-center justify-between">
                <flux:heading size="lg">Machine details</flux:heading>
            </div>
            <flux:input label="Hostname" disabled value="{{ $machineDetails?->name }}" />
            <flux:input label="IP address" disabled value="{{ $machineDetails?->ip_address }}" />
            <flux:input label="Status" disabled value="{{ $machineDetails?->status }}" />
            <flux:textarea label="Notes" disabled>{{ $machineDetails?->notes }}</flux:textarea>
            <flux:input label="Lab" disabled value="{{ $machineDetails?->lab?->name }}" />
            <flux:separator class="my-2" />
            <div class="flex items-center justify-between">
                <flux:button :href="route('machine.details', $machineDetails?->id ?? 1)">Full details</flux:button>
                <flux:button class="cursor-pointer" x-on:click="$flux.modals().close()">Close</flux:button>
            </div>
            @if ($machineDetails?->logs->count() > 0)
                <flux:separator class="my-2" />
                <div class="space-y-2">
                    <flux:heading size="sm">Recent logs</flux:heading>
                    <div class="flex flex-col gap-2 max-h-48 overflow-y-auto">
                        @foreach ($machineDetails->logs->take(10) as $log)
                            <div class="flex flex-col sm:flex-row gap-1 text-sm">
                                <flux:text variant="strong" class="text-xs">{{ $log->created_at->format('d/m/Y H:i:s') }}</flux:text>
                                <flux:text class="text-xs">{{ substr($log->message, 0, 50) . '...' }}</flux:text>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>

    <flux:modal name="bulk-delete-confirmation" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">Delete Multiple Machines</flux:heading>

            <flux:callout variant="danger" icon="x-circle" heading="Warning: This action cannot be undone!">
                You are about to permanently delete <strong>{{ $bulkDeleteCount }}</strong> {{ Str::plural('machine', $bulkDeleteCount) }} that match your current filter.
                <br><br>
                This will remove all machine records and their associated logs from the system.
                <br><br>
                <strong>This is typically done during the summer rebuild process when clearing out old machines before fresh installations.</strong>
            </flux:callout>

            <flux:separator class="my-4" />

            <div class="flex items-center justify-between">
                <flux:button
                    type="button"
                    x-on:click="$flux.modals().close()"
                >
                    Cancel
                </flux:button>
                <flux:button
                    variant="danger"
                    wire:click="bulkDelete"
                    wire:confirm="Are you absolutely sure you want to delete {{ $bulkDeleteCount }} {{ Str::plural('machine', $bulkDeleteCount) }}? This cannot be undone."
                >
                    Delete {{ $bulkDeleteCount }} {{ Str::plural('machine', $bulkDeleteCount) }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
