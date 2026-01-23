<section>
    <header>
        <h2 class="text-lg font-semibold text-text-primary flex items-center gap-2">
            <svg class="w-5 h-5 text-slt-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-text-secondary">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-text-secondary mb-2">{{ __('Current Password') }}</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-text-secondary mb-2">{{ __('New Password') }}</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-text-secondary mb-2">{{ __('Confirm Password') }}</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-lg">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slt-green"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
