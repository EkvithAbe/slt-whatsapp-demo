<nav x-data="{ open: false }" class="relative z-50 bg-glass-bg backdrop-blur-xl border-b border-glass-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo & Brand -->
            <div class="flex items-center">
                <a href="{{ route('chats.index') }}" class="flex items-center gap-3">
                    <img src="/images/slt-logo.png" alt="SLT Logo" class="h-10 w-auto" />
                    <span class="text-text-primary font-semibold text-lg hidden sm:block">SLT WhatsApp</span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-8 space-x-1">
                    <a href="{{ route('chats.index') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('chats.*') ? 'bg-slt-blue/20 text-slt-blue border border-slt-blue/30' : 'text-text-secondary hover:bg-dark-card hover:text-text-primary' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chats
                        </span>
                    </a>

                    <a href="{{ route('logs.index') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('logs.*') ? 'bg-slt-blue/20 text-slt-blue border border-slt-blue/30' : 'text-text-secondary hover:bg-dark-card hover:text-text-primary' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Logs
                        </span>
                    </a>
                </div>
            </div>

            <!-- User Menu (Desktop) -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 px-3 py-2 rounded-xl bg-dark-card hover:bg-dark-card/80 transition-all duration-200 text-text-primary border border-glass-border">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-sm font-semibold text-white shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-text-secondary">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-dark-card rounded-xl border border-glass-border shadow-xl overflow-hidden">
                            <div class="px-4 py-3 border-b border-glass-border">
                                <div class="text-sm font-medium text-text-primary">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-text-secondary">{{ Auth::user()->email }}</div>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-text-secondary hover:bg-glass-bg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-error-red hover:bg-error-red/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-xl bg-dark-card text-text-secondary hover:text-text-primary transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-dark-card border-t border-glass-border">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('chats.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('chats.*') ? 'bg-slt-blue/20 text-slt-blue' : 'text-text-secondary hover:bg-glass-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chats
            </a>

            <a href="{{ route('logs.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('logs.*') ? 'bg-slt-blue/20 text-slt-blue' : 'text-text-secondary hover:bg-glass-bg' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Logs
            </a>
        </div>

        <!-- Mobile User Info -->
        <div class="pt-4 pb-3 border-t border-glass-border px-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white font-semibold shadow-md">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-text-primary">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-text-secondary">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-text-secondary hover:bg-glass-bg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-error-red hover:bg-error-red/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
