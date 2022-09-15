<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>QR To Webhook</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
        @livewireStyles
    </head>
    <body>
        <div class="section">
            <div class="container">
                @yield('content')
            </div>
        </div>
        @livewireScripts
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
