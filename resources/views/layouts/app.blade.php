<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
    @livewireStyles
</head>
<body class="bg-zinc-50 dark:bg-zinc-900">
<flux:header class="flex mx-auto w-2/3 max-w-3xl">
    <flux:heading level="1" size="xl" class="ms-4">Kalkulator ocen</flux:heading>
    <flux:spacer/>
    <div class="flex gap-4 items-center">
        <flux:modal.trigger name="settings">
            <flux:button icon="cog-6-tooth"/>
        </flux:modal.trigger>

        <flux:switch x-data x-model="$flux.dark" label="Ciemny motyw"/>
    </div>

</flux:header>
<flux:main container class="max-w-3xl">
    {{ $slot }}
    <flux:toast/>
</flux:main>


@livewireScripts
@fluxScripts
</body>
</html>
