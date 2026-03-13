<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SLT WhatsApp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="appNotifications()" x-init="init()">
        <div class="min-h-screen bg-slt-ink">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <div class="pointer-events-none fixed right-4 top-4 z-[100] flex w-[22rem] max-w-[calc(100vw-2rem)] flex-col gap-2"
             aria-live="assertive"
             aria-atomic="true">
            <template x-for="error in errors" :key="error.key">
                <div x-transition
                     class="pointer-events-auto rounded-xl border border-red-300/30 bg-red-600 px-4 py-3 text-sm text-white shadow-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2">
                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 4h15.876c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L2.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="font-medium leading-5" x-text="error.message"></p>
                        </div>
                        <button type="button"
                                class="rounded-lg p-1 text-red-100 hover:bg-black/20"
                                @click="dismissError(error.key)"
                                aria-label="Dismiss error">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <script>
            function appNotifications() {
                return {
                    errors: [],

                    init() {
                        window.addEventListener('app:error', (event) => {
                            const key = String(event?.detail?.key || '').trim();
                            const message = String(event?.detail?.message || 'Something went wrong.');
                            this.upsertError(key, message);
                        });

                        window.addEventListener('app:error:clear', (event) => {
                            const key = String(event?.detail?.key || '').trim();
                            this.clearError(key);
                        });

                        @if (session('error'))
                            this.upsertError('session-error', @js(session('error')));
                        @endif
                    },

                    upsertError(key, message) {
                        const text = String(message || '').trim();
                        if (!text) return;

                        const normalizedKey = key || `message:${text.toLowerCase()}`;
                        const existing = this.errors.find((item) => item.key === normalizedKey);

                        if (existing) {
                            existing.message = text;
                            return;
                        }

                        this.errors.push({ key: normalizedKey, message: text });
                    },

                    clearError(key) {
                        if (!key) {
                            this.errors = [];
                            return;
                        }
                        this.errors = this.errors.filter((item) => item.key !== key);
                    },

                    dismissError(key) {
                        this.clearError(key);
                    },
                };
            }
        </script>
    </body>
</html>
