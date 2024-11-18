<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title with inertiaHead to dynamically manage the page title -->
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Inertia Head, to inject meta tags, title, and other head elements -->
    @inertiaHead

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite('resources/js/app.js')  <!-- This is enough to load the main JS file -->
</head>
<body class="font-sans antialiased">
@inertia  <!-- This will render your Inertia Vue pages -->
</body>
</html>
