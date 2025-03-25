<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Your App' }}</title>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Other CSS (e.g., Tailwind, Flux, etc.) -->
    <!-- Add any additional stylesheets here if needed -->
</head>
<body class="font-sans antialiased">
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>