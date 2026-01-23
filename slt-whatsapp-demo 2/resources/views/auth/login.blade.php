<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-text-primary">Welcome back</h2>
        <p class="text-text-secondary text-sm mt-1">Sign in to continue to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-text-secondary mb-2">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="you@example.com"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-text-secondary mb-2">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                placeholder="Enter your password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded bg-dark-bg border-glass-border text-slt-blue focus:ring-slt-blue/30" />
                <span class="ml-2 text-sm text-text-secondary">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-slt-blue hover:text-slt-blue/80" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold rounded-xl hover:opacity-90 transition-all shadow-lg">
            Sign In
        </button>

        <p class="text-center text-text-secondary text-sm">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-slt-blue hover:text-slt-blue/80 font-medium">Create one</a>
        </p>
    </form>
</x-guest-layout>
