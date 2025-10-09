<div>
    <div class="flex items-center justify-between gap-2 mb-6">
        <flux:heading size="lg">User Management</flux:heading>
        @if(auth()->user()->isAdmin())
            <flux:button variant="primary" icon="plus" wire:click="create">
                Add User
            </flux:button>
        @endif
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
                        @if(auth()->user()->isAdmin())
                            @if ($user->is_admin)
                                <flux:badge as="button" class="cursor-pointer" color="sky" wire:click="toggleAdmin({{ $user->id }})">Yes</flux:badge>
                            @else
                                <flux:badge as="button" class="cursor-pointer" wire:click="toggleAdmin({{ $user->id }})">No</flux:badge>
                            @endif
                        @else
                            @if ($user->is_admin)
                                <flux:badge color="sky">Yes</flux:badge>
                            @else
                                <flux:badge>No</flux:badge>
                            @endif
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        @if(auth()->user()->isAdmin())
                            <div class="flex items-center justify-end gap-2 cursor-pointer">
                                <flux:button
                                    name="edit-button-{{ $user->id }}"
                                    size="sm"
                                    icon="pencil"
                                    wire:click="edit({{ $user->id }})"
                                >
                                </flux:button>
                                <flux:button
                                    name="delete-button-{{ $user->id }}"
                                    size="sm"
                                    icon="trash"
                                    wire:click="delete({{ $user->id }})"
                                    wire:confirm="Are you sure you want to delete this user?"
                                    class="cursor-pointer"
                                    :disabled="$user->id === auth()->user()->id"
                                >
                                </flux:button>
                            </div>
                        @endif
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

                @if($userId !== auth()->user()->id)
                    <div class="mb-6">
                        <flux:checkbox
                            wire:model="isAdmin"
                            label="Administrator"
                        />
                    </div>
                @endif

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
