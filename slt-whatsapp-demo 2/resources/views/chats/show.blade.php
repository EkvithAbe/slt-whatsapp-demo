<x-app-layout>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[calc(100vh-5rem)]" x-data="chatApp({{ $contact->id }})" x-init="init()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
      {{-- Sidebar --}}
      <div class="lg:col-span-1 rounded-2xl bg-glass-bg backdrop-blur-xl overflow-hidden border border-glass-border shadow-xl flex flex-col h-full"
           :class="sidebarOpen ? '' : 'hidden lg:block'">
        <div class="p-4 border-b border-glass-border flex items-center justify-between flex-shrink-0">
          <div class="font-semibold text-text-primary text-lg flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-slt-green animate-pulse"></div>
            Chats
          </div>
          <button class="lg:hidden p-2 rounded-xl bg-dark-card text-text-secondary hover:text-text-primary transition-colors"
                  @click="sidebarOpen=false">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="divide-y divide-glass-border flex-1 overflow-y-auto">
          @foreach($contacts as $c)
            <a href="{{ route('chats.show', $c) }}"
               class="block p-4 transition-all duration-200 group {{ $c->id === $contact->id ? 'bg-slt-blue/10 border-l-4 border-slt-blue' : 'hover:bg-dark-card' }}">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white font-semibold flex-shrink-0 shadow-md">
                  {{ strtoupper(substr($c->name ?? $c->mobile, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-medium truncate {{ $c->id === $contact->id ? 'text-slt-blue' : 'text-text-primary' }}">
                    {{ $c->name ?? 'Unknown' }}
                  </div>
                  <div class="text-xs text-text-secondary truncate">{{ $c->mobile }}</div>
                </div>
                @if($c->id === $contact->id)
                  <div class="w-2 h-2 rounded-full bg-slt-green animate-pulse"></div>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      </div>

      {{-- Chat Panel --}}
      <div class="lg:col-span-2 rounded-2xl bg-glass-bg backdrop-blur-xl overflow-hidden border border-glass-border shadow-xl flex flex-col h-full">
        {{-- Chat Header --}}
        <div class="p-4 border-b border-glass-border flex items-center justify-between bg-dark-card/50 flex-shrink-0">
          <div class="flex items-center gap-3">
            <button class="lg:hidden p-2 rounded-xl bg-dark-card text-text-secondary hover:text-text-primary transition-colors"
                    @click="sidebarOpen=true">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>

            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center text-white font-semibold shadow-lg">
              {{ strtoupper(substr($contact->name ?? $contact->mobile, 0, 1)) }}
            </div>

            <div>
              <div class="font-semibold text-text-primary">{{ $contact->name ?? 'Unknown' }}</div>
              <div class="text-xs text-text-secondary flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $contact->mobile }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button
              class="px-4 py-2 rounded-xl border border-glass-border hover:bg-dark-card transition-all duration-200 flex items-center gap-2 text-sm font-medium text-text-secondary"
              @click="manualSync()"
              :disabled="syncing">
              <svg class="w-4 h-4" :class="syncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span x-text="syncing ? 'Syncing...' : 'Sync'"></span>
            </button>

            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-dark-card border border-glass-border">
              <div class="w-2.5 h-2.5 rounded-full transition-colors duration-300"
                   :class="online ? 'bg-slt-green animate-pulse' : 'bg-text-secondary'"></div>
              <span class="text-xs font-medium" :class="online ? 'text-slt-green' : 'text-text-secondary'"
                    x-text="online ? 'Connected' : 'Offline'"></span>
            </div>
          </div>
        </div>

        {{-- Messages Area --}}
        <div class="flex-1 min-h-0 p-4 space-y-3 overflow-y-auto bg-dark-bg/50" x-ref="scroll">
          {{-- Date Divider --}}
          <div class="flex justify-center" x-show="messages.length > 0">
            <span class="px-3 py-1 rounded-full bg-dark-card text-xs text-text-secondary border border-glass-border">
              Today
            </span>
          </div>

          <template x-for="m in messages" :key="m.id + '_' + m.uuid">
            <div class="flex" :class="m.direction === 'out' ? 'justify-end' : 'justify-start'">
              <div class="max-w-[75%] rounded-2xl px-4 py-2.5 shadow-lg"
                   :class="m.direction === 'out'
                        ? 'bg-gradient-to-br from-slt-blue to-slt-blue/80 text-white rounded-br-md'
                        : 'bg-dark-card border border-glass-border text-text-primary rounded-bl-md'">
                <div class="whitespace-pre-wrap break-words" x-text="m.body"></div>
                <div class="flex items-center justify-end gap-1 mt-1"
                     :class="m.direction === 'out' ? 'text-white/60' : 'text-text-secondary'">
                  <span class="text-[11px]" x-text="formatTime(m.sent_at)"></span>
                  <template x-if="m.direction === 'out'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </template>
                </div>
              </div>
            </div>
          </template>

          {{-- Empty State --}}
          <div class="flex flex-col items-center justify-center h-full text-center" x-show="messages.length === 0">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-slt-blue/20 to-slt-green/20 flex items-center justify-center mb-4">
              <svg class="w-10 h-10 text-slt-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <h4 class="font-medium text-text-primary mb-1">No messages yet</h4>
            <p class="text-sm text-text-secondary max-w-xs">
              Click <strong class="text-slt-blue">Sync</strong> to fetch messages or send a message to start the conversation.
            </p>
          </div>
        </div>

        {{-- Message Input --}}
        <div class="p-4 border-t border-glass-border bg-dark-card/50 flex-shrink-0">
          <form class="flex gap-3" @submit.prevent="send()">
            <div class="flex-1 relative">
              <input
                x-model="draft"
                placeholder="Type a message..."
                class="w-full rounded-2xl bg-dark-bg border-glass-border text-text-primary placeholder-text-secondary focus:border-slt-blue focus:ring-slt-blue/30 pr-12 py-3 transition-all duration-200"
                @keydown.enter.prevent="send()"
              />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </button>
            </div>

            <button
              type="submit"
              class="px-5 py-3 rounded-2xl bg-gradient-to-r from-slt-blue to-slt-green text-white hover:opacity-90 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg flex items-center gap-2"
              :disabled="sending || !draft.trim()">
              <template x-if="!sending">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
              </template>
              <template x-if="sending">
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
              </template>
              <span class="hidden sm:inline" x-text="sending ? 'Sending...' : 'Send'"></span>
            </button>
          </form>

          <div class="mt-2 text-xs text-text-secondary flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Press Enter to send. Auto-syncing with WhatsApp every 5 seconds.
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function chatApp(contactId) {
      return {
        sidebarOpen: false,
        online: true,
        sending: false,
        syncing: false,
        draft: '',
        messages: [],
        lastMessageCount: 0,
        lastMessageId: null,
        pollMs: 1500,
        timer: null,
        syncTimer: null,

        async init() {
          await this.manualSync();
          await this.load();
          this.timer = setInterval(() => this.poll(), this.pollMs);
          this.syncTimer = setInterval(() => this.autoSync(), 5000);
        },

        destroy() {
          if (this.timer) clearInterval(this.timer);
          if (this.syncTimer) clearInterval(this.syncTimer);
        },

        async autoSync() {
          try {
            await fetch(`/chats/${contactId}/sync`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            await this.poll();
          } catch (e) {}
        },

        async manualSync() {
          this.syncing = true;
          try {
            await fetch(`/chats/${contactId}/sync`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            await this.load();
          } finally {
            this.syncing = false;
          }
        },

        async poll() {
          try {
            const res = await fetch(`/chats/${contactId}/messages?limit=200`);
            const data = await res.json();
            const newMessages = data.messages || [];
            const newCount = newMessages.length;
            const newLastId = newMessages.length > 0 ? newMessages[newMessages.length - 1].id : null;

            if (newCount !== this.lastMessageCount || newLastId !== this.lastMessageId) {
              this.messages = newMessages;
              this.lastMessageCount = newCount;
              this.lastMessageId = newLastId;
              this.$nextTick(() => this.scrollToBottom());
            }
            this.online = true;
          } catch (e) {
            this.online = false;
          }
        },

        async load() {
          try {
            const res = await fetch(`/chats/${contactId}/messages?limit=200`);
            const data = await res.json();
            this.messages = data.messages || [];
            this.lastMessageCount = this.messages.length;
            this.lastMessageId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : null;
            this.$nextTick(() => this.scrollToBottom());
            this.online = true;
          } catch (e) {
            this.online = false;
          }
        },

        async send() {
          if (!this.draft.trim()) return;
          this.sending = true;
          const message = this.draft;
          this.draft = '';

          try {
            const res = await fetch(`/chats/${contactId}/send`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ message: message })
            });

            const data = await res.json();
            if (!res.ok) {
              alert(data.error || 'Send failed');
              this.draft = message;
            }
            await this.load();
          } catch (e) {
            this.draft = message;
            alert('Failed to send message');
          } finally {
            this.sending = false;
          }
        },

        scrollToBottom() {
          const el = this.$refs.scroll;
          if (el) el.scrollTop = el.scrollHeight;
        },

        formatTime(iso) {
          if (!iso) return '';
          const d = new Date(iso);
          return d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        }
      }
    }
  </script>
</x-app-layout>
