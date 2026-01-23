<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SLT WhatsApp') }} - Business Messaging Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-dark-bg text-text-primary overflow-x-hidden">
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-slt-blue/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-slt-green/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute -bottom-40 right-1/3 w-96 h-96 bg-slt-blue/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 bg-glass-bg backdrop-blur-xl border-b border-glass-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-3">
                        <img src="/images/slt-logo.png" alt="SLT Logo" class="h-10 w-auto" />
                        <span class="text-text-primary font-semibold text-lg">SLT WhatsApp</span>
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('chats.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-medium hover:opacity-90 transition-all shadow-lg">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-4 py-2 text-text-secondary hover:text-text-primary transition-colors font-medium">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-medium hover:opacity-90 transition-all shadow-lg">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative z-10 pt-20 pb-32 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slt-blue/10 border border-slt-blue/30 mb-6">
                        <div class="w-2 h-2 rounded-full bg-slt-green animate-pulse"></div>
                        <span class="text-sm text-slt-blue font-medium">Powered by SLT-Mobitel</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        <span class="text-text-primary">Business Messaging</span>
                        <br />
                        <span class="bg-gradient-to-r from-slt-blue to-slt-green bg-clip-text text-transparent">Made Simple</span>
                    </h1>

                    <p class="text-lg text-text-secondary mb-8 max-w-xl mx-auto lg:mx-0">
                        Connect with your customers through WhatsApp Business API. Send messages, manage conversations, and grow your business with Sri Lanka's leading telecom provider.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ route('chats.index') }}"
                               class="px-8 py-4 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-xl shadow-slt-blue/30 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Open Chats
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="px-8 py-4 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-xl shadow-slt-blue/30 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Start Free Trial
                            </a>
                            <a href="{{ route('login') }}"
                               class="px-8 py-4 rounded-2xl bg-dark-card border border-glass-border text-text-primary font-semibold hover:bg-glass-bg transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Sign In
                            </a>
                        @endauth
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-6">
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-text-primary">99.9%</div>
                            <div class="text-sm text-text-secondary">Uptime</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-text-primary">24/7</div>
                            <div class="text-sm text-text-secondary">Support</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-3xl font-bold text-text-primary">10K+</div>
                            <div class="text-sm text-text-secondary">Messages/Day</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image / Chat Preview -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-slt-blue/20 to-slt-green/20 rounded-3xl blur-2xl"></div>
                    <div class="relative bg-glass-bg backdrop-blur-xl rounded-3xl border border-glass-border shadow-2xl overflow-hidden">
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-glass-border flex items-center gap-3 bg-dark-card/50">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white font-semibold shadow-lg">
                                C
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-text-primary">Customer Support</div>
                                <div class="text-xs text-slt-green flex items-center gap-1">
                                    <div class="w-1.5 h-1.5 rounded-full bg-slt-green animate-pulse"></div>
                                    Online
                                </div>
                            </div>
                        </div>

                        <!-- Chat Messages -->
                        <div class="p-4 space-y-3 bg-dark-bg/50 min-h-[280px]">
                            <div class="flex justify-start">
                                <div class="max-w-[80%] rounded-2xl rounded-bl-md px-4 py-2.5 bg-dark-card border border-glass-border">
                                    <p class="text-text-primary text-sm">Hi! How can I help you today?</p>
                                    <p class="text-xs text-text-secondary mt-1">09:30 AM</p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <div class="max-w-[80%] rounded-2xl rounded-br-md px-4 py-2.5 bg-gradient-to-br from-slt-blue to-slt-blue/80 text-white">
                                    <p class="text-sm">I'd like to know about your business plans</p>
                                    <p class="text-xs text-white/60 mt-1 flex items-center justify-end gap-1">
                                        09:31 AM
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </p>
                                </div>
                            </div>

                            <div class="flex justify-start">
                                <div class="max-w-[80%] rounded-2xl rounded-bl-md px-4 py-2.5 bg-dark-card border border-glass-border">
                                    <p class="text-text-primary text-sm">Great choice! We have several packages starting from Rs. 2,500/month with unlimited messaging.</p>
                                    <p class="text-xs text-text-secondary mt-1">09:32 AM</p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <div class="max-w-[80%] rounded-2xl rounded-br-md px-4 py-2.5 bg-gradient-to-br from-slt-blue to-slt-blue/80 text-white">
                                    <p class="text-sm">That sounds perfect! Sign me up</p>
                                    <p class="text-xs text-white/60 mt-1 flex items-center justify-end gap-1">
                                        09:33 AM
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input -->
                        <div class="p-4 border-t border-glass-border bg-dark-card/50">
                            <div class="flex gap-3">
                                <div class="flex-1 px-4 py-3 rounded-2xl bg-dark-bg border border-glass-border text-text-secondary text-sm">
                                    Type a message...
                                </div>
                                <button class="px-4 py-3 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="relative z-10 py-20 px-4 sm:px-6 lg:px-8 bg-dark-card/30">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-text-primary mb-4">
                    Everything You Need to Connect
                </h2>
                <p class="text-text-secondary max-w-2xl mx-auto">
                    Powerful features designed to help your business communicate effectively with customers through WhatsApp.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">Real-time Messaging</h3>
                    <p class="text-text-secondary">
                        Send and receive messages instantly with automatic synchronization every 5 seconds.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">Contact Management</h3>
                    <p class="text-text-secondary">
                        Organize and manage all your business contacts in one centralized dashboard.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">Secure & Reliable</h3>
                    <p class="text-text-secondary">
                        Enterprise-grade security with end-to-end encryption powered by SLT infrastructure.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">API Logs & Analytics</h3>
                    <p class="text-text-secondary">
                        Monitor all API calls, track message delivery, and analyze performance metrics.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">Mobile Friendly</h3>
                    <p class="text-text-secondary">
                        Fully responsive design that works seamlessly on desktop, tablet, and mobile devices.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border hover:border-slt-blue/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-2">Instant Setup</h3>
                    <p class="text-text-secondary">
                        Get started in minutes with easy configuration and intuitive user interface.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative z-10 py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-slt-blue/30 to-slt-green/30 rounded-3xl blur-2xl"></div>
                <div class="relative bg-glass-bg backdrop-blur-xl rounded-3xl border border-glass-border p-8 sm:p-12 text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center mb-6 shadow-xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-text-primary mb-4">
                        Ready to Transform Your Business Communication?
                    </h2>
                    <p class="text-text-secondary mb-8 max-w-2xl mx-auto">
                        Join thousands of businesses across Sri Lanka who trust SLT WhatsApp for their customer communication needs.
                    </p>
                    @auth
                        <a href="{{ route('chats.index') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Get Started Now
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 py-12 px-4 sm:px-6 lg:px-8 border-t border-glass-border bg-dark-card/30">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <img src="/images/slt-logo.png" alt="SLT Logo" class="h-8 w-auto" />
                    <span class="text-text-secondary text-sm">SLT WhatsApp Business Platform</span>
                </div>

                <div class="flex items-center gap-6 text-sm text-text-secondary">
                    <span>&copy; {{ date('Y') }} SLT-Mobitel. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
