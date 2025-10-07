<div>
    <div class="flex items-center justify-between gap-2 mb-6">
        <flux:heading size="lg">Lab Management</flux:heading>
        @if(auth()->user()->isAdmin())
            <flux:button variant="primary" icon="plus" wire:click="create">
                Add Lab
            </flux:button>
        @endif
    </div>

    <flux:input
        wire:model.live="filter"
        icon="magnifying-glass"
        placeholder="Filter labs..."
        class="mb-4"
    />

    <flux:separator class="my-4" />

    <flux:table :paginate="$labs">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Machines</flux:table.column>
            <flux:table.column>Notes</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($labs as $lab)
                <flux:table.row :key="$lab->id">
                    <flux:table.cell variant="strong">{{ $lab->name }}</flux:table.cell>
                    <flux:table.cell>{{ $lab->machines_count }}</flux:table.cell>
                    <flux:table.cell>{{ Str::limit($lab->notes, 50) }}</flux:table.cell>
                    <flux:table.cell align="end">
                        @if(auth()->user()->isAdmin())
                            <div class="flex items-center justify-end gap-2">
                                <flux:button
                                    size="sm"
                                    icon="pencil"
                                    wire:click="edit({{ $lab->id }})"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    icon="trash"
                                    wire:click="delete({{ $lab->id }})"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="lab-form" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $labId ? 'Edit Lab' : 'Create Lab' }}
            </flux:heading>

            <form wire:submit="save">
                <flux:input
                    wire:model="name"
                    label="Name"
                    class="mb-6"
                    required
                />

                <flux:textarea
                    wire:model="notes"
                    label="Notes"
                    rows="4"
                    class="mb-6"
                />

                <div class="flex items-center justify-between">
                    <flux:button type="submit" variant="primary">
                        {{ $labId ? 'Update' : 'Create' }}
                    </flux:button>
                    <flux:button
                        type="button"
                        x-on:click="$flux.modals().close()"
                    >
                        Cancel
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="delete-lab-confirmation" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">Delete Lab with Machines</flux:heading>

            @if($labToDelete)
                <flux:callout variant="warning">
                    This lab has <strong>{{ $labToDelete->machines->count() }}</strong> {{ Str::plural('machine', $labToDelete->machines->count()) }} associated with it.
                    Choose how you want to handle them:
                </flux:callout>

                <flux:separator class="my-4" />

                <div class="space-y-6">
                    <div class="space-y-4">
                        <flux:heading size="sm">Option 1: Reassign Machines</flux:heading>
                        <flux:text>Move all machines to another lab before deleting this one.</flux:text>

                        <flux:select
                            wire:model="reassignLabId"
                            label="Reassign machines to"
                            placeholder="Choose a lab..."
                        >
                            <option value="">Choose a lab...</option>
                            @foreach ($labs->where('id', '!=', $labToDelete->id) as $lab)
                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:button
                            variant="primary"
                            wire:click="confirmDeleteWithReassign"
                            class="w-full"
                        >
                            Reassign & Delete Lab
                        </flux:button>
                    </div>

                    <flux:separator class="my-4" />

                    <div class="space-y-4">
                        <flux:heading size="sm">Option 2: Delete Everything</flux:heading>
                        <flux:text>Delete the lab and all {{ $labToDelete->machines->count() }} {{ Str::plural('machine', $labToDelete->machines->count()) }}. This cannot be undone.</flux:text>

                        <flux:button
                            variant="danger"
                            wire:click="confirmDeleteWithMachines"
                            wire:confirm="Are you sure you want to delete the lab AND all {{ $labToDelete->machines->count() }} {{ Str::plural('machine', $labToDelete->machines->count()) }}? This action cannot be undone."
                            class="w-full"
                        >
                            Delete Lab & All Machines
                        </flux:button>
                    </div>

                    <flux:separator class="my-4" />

                    <flux:button
                        type="button"
                        x-on:click="$flux.modals().close()"
                        class="w-full"
                    >
                        Cancel
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
