<div>
    <div class="flex items-center justify-between gap-2 mb-6">
        <flux:heading size="lg">User Management</flux:heading>
        <flux:button variant="primary" icon="plus" wire:click="create">
            Add User
        </flux:button>
    </div>

    <flux:input
        wire:model.live="filter"
        icon="magnifying-glass"
        placeholder="Search users..."
        class="mb-4"
    />

    <flux:separator class="my-4" />

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Admin</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($user->is_admin)
                            <flux:badge color="green">Yes</flux:badge>
                        @else
                            <flux:badge>No</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex items-center justify-end gap-2">
                            <flux:button
                                size="sm"
                                icon="pencil"
                                wire:click="edit({{ $user->id }})"
                            >
                                Edit
                            </flux:button>
                            <flux:button
                                size="sm"
                                icon="trash"
                                wire:click="delete({{ $user->id }})"
                                wire:confirm="Are you sure you want to delete this user?"
                            >
                                Delete
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="user-form">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $userId ? 'Edit User' : 'Create User' }}
            </flux:heading>

            <form wire:submit="save">
                <flux:input
                    wire:model="name"
                    label="Name"
                    class="mb-6"
                    required
                />

                <flux:input
                    wire:model="email"
                    label="Email"
                    type="email"
                    class="mb-6"
                    required
                />

                <flux:input
                    wire:model="password"
                    label="Password"
                    type="password"
                    class="mb-6"
                    :required="!$userId"
                    placeholder="{{ $userId ? 'Leave blank to keep current password' : '' }}"
                />

                <flux:checkbox
                    wire:model="isAdmin"
                    label="Administrator"
                    class="mb-6"
                />

                <div class="flex items-center justify-between">
                    <flux:button type="submit" variant="primary">
                        {{ $userId ? 'Update' : 'Create' }}
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
