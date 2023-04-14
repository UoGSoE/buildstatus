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
        <x-nav.navbar>
            <x-nav.navbar-left>
                <x-nav.navbar-logo>
                    LOGO
                </x-nav.navbar-logo>
                <x-nav.navbar-link href="#" active="true">
                    Dashboard
                </x-nav.navbar-link>
                <x-nav.navbar-link href="#">
                    Manage Access
                </x-nav.navbar-link>
                <x-nav.navbar-link href="#">
                    Manage Tags
                </x-nav.navbar-link>
            </x-nav.navbar-left>
            <x-nav.navbar-right>
                <x-nav.navbar-dropdown>
                    <x-slot:trigger>Options</x-slot:trigger>
                    <x-nav.navbar-dropdown-link href="#">
                        Account settings
                    </x-nav.navbar-dropdown-link>
                    <x-nav.navbar-dropdown-link href="#">
                        Support
                    </x-nav.navbar-dropdown-link>
                    <x-nav.navbar-dropdown-link href="#">
                        License
                    </x-nav.navbar-dropdown-link>
                    <form method="POST" action="#" role="none">
                        <button type="submit"
                            class="text-gray-700 block w-full px-4 py-2 text-left text-sm  hover:bg-gray-100"
                            role="menuitem" tabindex="-1" id="menu-item-3">Sign out</button>
                    </form>
                </x-nav.navbar-dropdown>
            </x-nav.navbar-right>
        </x-nav.navbar>

        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

    </div>
    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
