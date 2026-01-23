<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-dark-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Animated Background -->
            <div class="fixed inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-slt-blue/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-slt-green/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Logo -->
            <div class="relative z-10 mb-8">
                <a href="/" class="flex flex-col items-center gap-4">
                    <img src="/images/slt-logo.png" alt="SLT Logo" class="h-20 w-auto" />
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-text-primary">SLT WhatsApp</h1>
                        <p class="text-text-secondary text-sm">Business Messaging Platform</p>
                    </div>
                </a>
            </div>

            <!-- Card -->
            <div class="relative z-10 w-full sm:max-w-md px-6 py-8 bg-glass-bg backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-2xl border border-glass-border">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="relative z-10 mt-8 text-text-secondary text-sm">
                Powered by SLT Digital Platform
            </p>
        </div>
    </body>
</html>
