<div>
    <div class="space-y-6" wire:poll.60s>
        <div class="flex flex-row items-center justify-between">
            <flux:heading size="lg">Machine details</flux:heading>
        </div>
        <flux:card size="sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div class="flex flex-col gap-2">
                    <flux:text>Hostname:</flux:text>
                    <flux:text variant="strong">{{ $machine?->name }}</flux:text>
                </div>
                <div class="flex flex-col gap-2">
                    <flux:text>IP address:</flux:text>
                    <flux:text variant="strong">{{ $machine?->ip_address }}</flux:text>
                </div>
                <div class="flex flex-col gap-2">
                    <flux:text>Status:</flux:text>
                    <flux:text variant="strong">{{ $machine?->status }}</flux:text>
                </div>
                <div class="flex flex-col gap-2">
                    <flux:text>Lab:</flux:text>
                    <flux:text variant="strong">{{ $machine?->lab?->name }}</flux:text>
                </div>
            </div>
                <div class="flex flex-col gap-2 mt-4">
                    <flux:text>Notes:</flux:text>
                    <flux:text variant="strong">{{ $machine?->notes }}</flux:text>
                </div>
        </flux:card>
        <flux:separator class="my-2" />
        <flux:heading size="lg">Logs</flux:heading>
        @if ($logs->count() > 0)
            <div class="flex flex-col gap-2">
                @foreach ($logs as $log)
                    <div class="flex flex-col md:flex-row gap-2">
                        <flux:text variant="strong">{{ $log->created_at->format('d/m/Y H:i:s') }}</flux:text>
                        <flux:text>{{ $log->message }}</flux:text>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <flux:pagination :paginator="$logs" />
            </div>
        @else
            <flux:text>No logs found</flux:text>
        @endif
    </div>
</div>
