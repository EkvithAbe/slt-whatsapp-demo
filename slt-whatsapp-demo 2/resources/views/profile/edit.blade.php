<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Page Header --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-text-primary">Profile Settings</h1>
                    <p class="text-text-secondary">Manage your account settings and preferences</p>
                </div>
            </div>

            {{-- Profile Information --}}
            <div class="p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border shadow-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border shadow-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-6 rounded-2xl bg-glass-bg backdrop-blur-xl border border-glass-border shadow-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
