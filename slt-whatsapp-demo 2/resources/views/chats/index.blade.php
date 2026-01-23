<x-app-layout>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[calc(100vh-5rem)]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
      {{-- Contact List --}}
      <div class="lg:col-span-1 rounded-2xl bg-glass-bg backdrop-blur-xl overflow-hidden border border-glass-border shadow-xl flex flex-col h-full">
        <div class="p-4 border-b border-glass-border flex items-center justify-between flex-shrink-0">
          <div class="font-semibold text-text-primary text-lg flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-slt-green animate-pulse"></div>
            Chats
          </div>
          <button
            onclick="document.getElementById('addContact').showModal()"
            class="px-4 py-2 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white hover:opacity-90 transition-all duration-200 flex items-center gap-2 text-sm font-medium shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Contact
          </button>
        </div>

        <div class="divide-y divide-glass-border flex-1 overflow-y-auto">
          @forelse($contacts as $c)
            <a href="{{ route('chats.show', $c) }}"
               class="block p-4 hover:bg-dark-card transition-all duration-200 group">
              <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white font-semibold text-lg flex-shrink-0 shadow-lg">
                  {{ strtoupper(substr($c->name ?? $c->mobile, 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <div class="font-semibold text-text-primary truncate group-hover:text-slt-blue transition-colors">
                      {{ $c->name ?? 'Unknown' }}
                    </div>
                    @if($c->lastMessage?->sent_at)
                      <div class="text-xs text-text-secondary flex-shrink-0 ml-2">
                        {{ $c->lastMessage->sent_at->format('H:i') }}
                      </div>
                    @endif
                  </div>

                  <div class="text-sm text-text-secondary truncate mt-0.5">
                    @if($c->lastMessage)
                      @if($c->lastMessage->direction === 'out')
                        <span class="text-slt-green">
                          <svg class="w-4 h-4 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                          </svg>
                        </span>
                      @endif
                      {{ $c->lastMessage->body }}
                    @else
                      <span class="text-text-secondary italic">No messages yet</span>
                    @endif
                  </div>

                  <div class="text-xs text-text-secondary mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $c->mobile }}
                  </div>
                </div>
              </div>
            </a>
          @empty
            <div class="p-8 text-center">
              <div class="w-16 h-16 mx-auto rounded-full bg-dark-card flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div class="text-text-secondary font-medium">No contacts yet</div>
              <div class="text-sm text-text-secondary mt-1">Add a contact to start chatting</div>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Empty State --}}
      <div class="lg:col-span-2 rounded-2xl bg-glass-bg backdrop-blur-xl overflow-hidden border border-glass-border shadow-xl">
        <div class="h-full flex flex-col items-center justify-center p-8 min-h-[60vh]">
          <div class="w-24 h-24 rounded-full bg-gradient-to-br from-slt-blue/20 to-slt-green/20 flex items-center justify-center mb-6 shadow-2xl">
            <svg class="w-12 h-12 text-slt-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-text-primary mb-2">Select a conversation</h3>
          <p class="text-text-secondary text-center max-w-sm">
            Choose a contact from the list to view your conversation history and send messages.
          </p>
          <div class="mt-6 p-4 bg-dark-card rounded-xl border border-glass-border">
            <div class="text-sm text-text-secondary flex items-center gap-2">
              <svg class="w-5 h-5 text-slt-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Messages auto-sync every 5 seconds</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Add Contact Modal --}}
  <dialog id="addContact" class="rounded-2xl p-0 w-full max-w-md backdrop:bg-black/70 shadow-2xl bg-transparent">
    <form method="POST" action="{{ route('contacts.store') }}" class="bg-dark-card rounded-2xl overflow-hidden border border-glass-border">
      @csrf
      <div class="p-5 bg-gradient-to-r from-slt-blue to-slt-green">
        <h3 class="font-semibold text-white text-lg">Add New Contact</h3>
        <p class="text-white/80 text-sm mt-1">Enter the contact details below</p>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-medium text-text-secondary mb-2">Name</label>
          <input
            name="name"
            placeholder="John Doe"
            class="w-full rounded-xl bg-dark-bg border-glass-border text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 transition-colors"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-text-secondary mb-2">Mobile Number</label>
          <input
            name="mobile"
            required
            placeholder="94711234567 or 0711234567"
            class="w-full rounded-xl bg-dark-bg border-glass-border text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 transition-colors font-mono"
          />
          <p class="text-xs text-text-secondary mt-2">Format: 9471xxxxxxx or 07xxxxxxxx</p>
        </div>
      </div>

      <div class="p-5 border-t border-glass-border flex justify-end gap-3 bg-glass-bg">
        <button
          type="button"
          onclick="document.getElementById('addContact').close()"
          class="px-4 py-2 rounded-xl border border-glass-border text-text-secondary hover:bg-dark-card transition-colors">
          Cancel
        </button>
        <button class="px-5 py-2 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white hover:opacity-90 transition-opacity shadow-lg">
          Save Contact
        </button>
      </div>
    </form>
  </dialog>
</x-app-layout>
