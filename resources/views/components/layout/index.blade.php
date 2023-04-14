<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @livewireStyles
</head>

<body class="h-full font-sans">
    <div class="min-h-full">
        @auth
            <x-nav.navbar>
                <x-nav.navbar-left>
                    <x-nav.navbar-link href="/" class="text-white">
                        Build Status
                    </x-nav.navbar-link>
                    <x-nav.navbar-dropdown>
                        <x-slot:trigger>More</x-slot:trigger>
                        <x-nav.navbar-dropdown-link href="{{ route('admin.access') }}">
                            Manage Access
                        </x-nav.navbar-dropdown-link>
                        <x-nav.navbar-dropdown-link href="{{ route('admin.tags') }}">
                            Manage Tags
                        </x-nav.navbar-dropdown-link>
                    </x-nav.navbar-dropdown>
                </x-nav.navbar-left>
                <x-nav.navbar-right>
                    <form method="POST" action="#" role="none">
                        @csrf
                        <x-button.plain type="submit" role="menuitem" tabindex="-1" id="menu-item-3">
                            Sign out
                        </x-button.plain>
                    </form>
                </x-nav.navbar-right>
            </x-nav.navbar>
        @endauth
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>
    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
