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
            <flux:table.column>Username</flux:table.column>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Admin</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">{{ $user->username }}</flux:table.cell>
                    <flux:table.cell>{{ $user->forenames }} {{ $user->surname }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($user->is_admin)
                            <flux:badge as="button" color="sky" wire:click="toggleAdmin({{ $user->id }})">Yes</flux:badge>
                        @else
                            <flux:badge as="button" wire:click="toggleAdmin({{ $user->id }})">No</flux:badge>
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

    <flux:modal name="user-form" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $userId ? 'Edit User' : 'Create User' }}
            </flux:heading>

            <form wire:submit="save">
                <flux:input
                    wire:model="username"
                    label="Username"
                    class="mb-6"
                    required
                />

                <flux:input
                    wire:model="forenames"
                    label="Forenames"
                    class="mb-6"
                    required
                />

                <flux:input
                    wire:model="surname"
                    label="Surname"
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

                <div class="mb-6">
                    <flux:checkbox
                        wire:model="isAdmin"
                        label="Administrator"
                    />
                </div>

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
