<section>
    <header>
        <h2 class="text-lg font-semibold text-text-primary flex items-center gap-2">
            <svg class="w-5 h-5 text-slt-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-text-secondary">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-text-secondary mb-2">{{ __('Name') }}</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-text-secondary mb-2">{{ __('Email') }}</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="w-full px-4 py-3 bg-dark-bg border border-glass-border rounded-xl text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 focus:ring-2 transition-all"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-text-secondary">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-slt-blue hover:text-slt-blue/80 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slt-blue">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-slt-green">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white font-semibold hover:opacity-90 transition-all shadow-lg">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
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
