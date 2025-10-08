<div>
    <div class="flex items-center justify-between gap-2 mb-6">
        <flux:heading size="lg">API Tokens</flux:heading>
        <flux:button variant="primary" icon="plus" wire:click="create">
            Create Token
        </flux:button>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="flex items-center justify-end gap-2">
        <flux:field variant="inline">
            <flux:label>View all users tokens</flux:label>
            <flux:switch wire:model.live="viewAllKeys" />
        </flux:field>
    </div>
    @endif

    <flux:separator class="my-4" />

    <flux:table>
        <flux:table.columns>
            @if($viewAllKeys && auth()->user()->isAdmin())
                <flux:table.column>User</flux:table.column>
            @endif
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Created</flux:table.column>
            <flux:table.column>Last Used</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($tokens as $token)
                <flux:table.row wire:key="token-{{ $token->id }}">
                    @if($viewAllKeys && auth()->user()->isAdmin())
                        <flux:table.cell>
                            {{ $token->tokenable->username ?? 'Unknown' }}
                            @if($token->tokenable->is_admin ?? false)
                                <flux:badge color="sky" size="sm">Admin</flux:badge>
                            @endif
                        </flux:table.cell>
                    @endif
                    <flux:table.cell variant="strong">{{ $token->name }}</flux:table.cell>
                    <flux:table.cell>{{ $token->created_at->format('M j, Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>
                        @if($token->last_used_at)
                            {{ $token->last_used_at->diffForHumans() }}
                        @else
                            <flux:badge color="zinc">Never</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button
                            size="sm"
                            icon="trash"
                            wire:click="revoke({{ $token->id }})"
                            wire:confirm="Are you sure you want to revoke this token? This action cannot be undone."
                        >
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="{{ $viewAllKeys && auth()->user()->isAdmin() ? 5 : 4 }}" class="text-center text-zinc-500">
                        No API tokens created yet.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Create Token Modal --}}
    <flux:modal name="token-form" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">Create API Token</flux:heading>

            <form wire:submit="save">
                <flux:input
                    wire:model="tokenName"
                    label="Token Name"
                    description="A descriptive name for this token (e.g., 'Production Server', 'Testing')"
                    placeholder="Enter token name"
                    class="mb-6"
                    required
                />

                <div class="flex items-center justify-between">
                    <flux:button type="submit" variant="primary">
                        Create Token
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

    {{-- Display Plaintext Token Modal --}}
    <flux:modal name="token-display" variant="flyout">
        <div class="space-y-6">
            <flux:heading size="lg">Token Created Successfully</flux:heading>

            <flux:callout variant="warning" icon="exclamation-triangle">
                <strong>Important:</strong> Copy this token now. For security reasons, it won't be shown again.
            </flux:callout>

            <flux:input
                :value="$plaintextToken"
                label="API Token"
                icon="key"
                readonly
                copyable
            />

            <div class="flex items-center justify-end">
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="closeTokenDisplay"
                >
                    Done
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
