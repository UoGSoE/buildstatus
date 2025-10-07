<div>
    <div class="flex items-center justify-between gap-2 mb-6">
        <flux:heading size="lg">Lab Management</flux:heading>
        <flux:button variant="primary" icon="plus" wire:click="create">
            Add Lab
        </flux:button>
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
                                wire:confirm="Are you sure you want to delete this lab?{{ $lab->machines_count > 0 ? ' This will also orphan ' . $lab->machines_count . ' machine(s) which are still associated with this lab.' : '' }}"
                            >
                                Delete
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="lab-form">
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
</div>
