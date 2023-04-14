<div class="bg-white p-4 shadow">
    <header class="bg-white shadow">
        <x-layout.level>
            <x-layout.level-section>
                <h1 class="justify-self-start text-3xl font-bold tracking-tight text-gray-900">Access Manager</h1>
            </x-layout.level-section>
            <x-layout.level-section>
                <x-button.plain wire:click.prevent="$toggle('showCreateForm')">Add</x-button.plain>
            </x-layout.level-section>
        </x-layout.level>
    </header>
    <div class="mt-4">

        @if ($showCreateForm)
            <form class="w-full sm:w-1/2">
                <div class="space-y-12">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <x-form.text wire:model.defer="username" label="Username (GUID)" id="guid" with-button="true">
                                    <x-slot:button class="blah-de-blah" wire:click.prevent="lookupUser">
                                        Look Up
                                    </x-slot:button>
                                </x-form.text>
                                <x-form.text wire:model.defer="email" label="Email" id="email" />
                                <x-form.text wire:model.defer="surname" label="Surname" id="surname" />
                                <x-form.text wire:model.defer="forenames" label="Forename" id="forename" />
                                <x-form.text wire:model.defer="description" label="Short description (eg, 'Based in JWS')" id="description" max="200" />
                                <x-button.primary type="submit" class="mt-8" wire:click.prevent="createUser">Create new user</x-button.primary>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($users as $user)
                <li wire:key="user-id-{{ $user->id }}"
                    class="col-span-1 divide-y divide-gray-200 rounded-lg bg-slate-50 shadow">
                    <div class="flex w-full items-center justify-between space-x-6 p-6">
                        <div class="flex-1 truncate">
                            <div class="flex items-center space-x-3">
                                <h3 class="truncate text-sm font-medium text-gray-900">{{ $user->full_name }}</h3>
                                <span
                                    class="inline-block flex-shrink-0 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Admin</span>
                            </div>
                            <p class="mt-1 truncate text-sm text-gray-500">{{ $user->description }}</p>
                        </div>
                        <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300"
                            src="https://placekitten.com/256/256"
                            alt="">
                    </div>
                    <div>
                        <div class="-mt-px flex divide-x divide-gray-200">
                            <div class="flex w-0 flex-1">
                                <a href="mailto:{{ $user->email }}"
                                    class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true">
                                        <path
                                            d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                        <path
                                            d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                    </svg>
                                    Email
                                </a>
                            </div>
                            <div class="-ml-px flex w-0 flex-1">
                                <button wire:click.prevent="deleteUser({{ $user->id }})"
                                    class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900 hover:bg-red-200 hover:text-red-900">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-red-300" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
            <!-- More people... -->
        </ul>
    </div>
</div>
