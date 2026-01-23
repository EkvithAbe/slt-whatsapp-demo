<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-text-primary">Create an account</h2>
        <p class="text-text-secondary text-sm mt-1">Join SLT WhatsApp messaging platform</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-text-secondary mb-2">Full Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="John Doe"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-text-secondary mb-2">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="you@example.com"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-text-secondary mb-2">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Create a strong password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-text-secondary mb-2">Confirm Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm your password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold rounded-xl hover:opacity-90 transition-all shadow-lg"
        >
            Create Account
        </button>

        <!-- Login Link -->
        <p class="text-center text-text-secondary text-sm">
            Already have an account?
            <a href="{{ route('login') }}" class="text-slt-blue hover:text-slt-blue/80 font-medium">Sign in</a>
        </p>
    </form>
</x-guest-layout>
