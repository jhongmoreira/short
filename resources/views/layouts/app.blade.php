<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f0f11">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', 'Short') · encurtador de links</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:500" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col">
    <header class="px-4 pt-6 sm:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-baseline gap-0.5 text-lg font-bold tracking-tight">
            short<span class="text-accent">.</span>
        </a>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8">
        @yield('content')
    </main>

    <footer class="px-4 pb-6 text-center text-sm text-muted sm:px-8">
        @yield('footer')
    </footer>
</body>
</html>
