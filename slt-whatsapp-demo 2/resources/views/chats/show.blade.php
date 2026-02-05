<x-app-layout>
  <div class="max-w-7xl mx-auto p-4" x-data="chatApp({{ $contact->id }})" x-init="init()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      {{-- Sidebar --}}
      <div class="lg:col-span-1 rounded-2xl bg-white/5 border border-white/10 overflow-hidden"
           :class="sidebarOpen ? '' : 'hidden lg:block'">
        <div class="p-4 border-b border-white/10 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-slt-accent"></span>
            <span class="font-semibold text-white">Chats</span>
          </div>
          <button class="lg:hidden px-3 py-2 rounded-xl border border-white/10 text-slt-muted hover:text-white" @click="sidebarOpen=false">Close</button>
        </div>

        <div id="chatList" x-ref="chatList" class="divide-y divide-white/5 max-h-[calc(100vh-200px)] overflow-y-auto scrollbar-dark">
          @include('chats.partials.list-items', [
            'contacts' => $contacts,
            'showPreview' => false,
            'showLock' => true,
            'showActive' => true,
            'activeContactId' => $contact->id,
          ])
        </div>
      </div>

      {{-- Chat panel --}}
      <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 overflow-hidden flex flex-col">
        <!-- Chat Header -->
        <div class="p-4 border-b border-white/10 flex flex-col gap-3 bg-white/5">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <button class="lg:hidden px-3 py-2 rounded-xl border border-white/10 text-slt-muted hover:text-white" @click="sidebarOpen=true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <div class="w-10 h-10 rounded-full bg-slt-info flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr($contact->name ?? $contact->mobile, 0, 1)) }}
              </div>
              <div>
                <div class="font-semibold text-white">{{ $contact->name ?? $contact->mobile }}</div>
                <div class="flex items-center gap-1 text-xs text-slt-muted">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  {{ $contact->mobile }}
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <!-- Save Contact Button -->
              <button @click="openSaveContact()" class="px-3 py-2 rounded-xl border border-slt-accent text-slt-accent hover:bg-slt-accent/10 text-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Save Contact
              </button>

              <!-- Take Chat (Lock) Button -->
              <button
                @click="acquireLock()"
                class="px-3 py-2 rounded-xl text-sm flex items-center gap-2 transition-all"
                :class="lock.locked_by_me
                  ? 'bg-slt-accent text-white'
                  : (lock.locked && !lock.locked_by_me
                    ? 'bg-red-500/20 border border-red-500/50 text-red-400 cursor-not-allowed'
                    : 'border border-white/10 text-slt-muted hover:text-white hover:bg-white/5')"
                :disabled="lock.locked && !lock.locked_by_me"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span x-text="lock.locked_by_me ? 'Chat Locked by You' : (lock.locked ? 'Locked' : 'Take Chat')"></span>
              </button>

              <!-- Sync Button -->
              <button class="px-3 py-2 rounded-xl border border-white/10 text-slt-muted hover:text-white hover:bg-white/5 text-sm flex items-center gap-2 transition-all" @click="manualSync()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync
              </button>

              <!-- Connection Status -->
              <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-white/10">
                <span class="w-2 h-2 rounded-full" :class="online ? 'bg-slt-accent status-online' : 'bg-slt-muted'"></span>
                <span class="text-sm" :class="online ? 'text-slt-accent' : 'text-slt-muted'" x-text="online ? 'Connected' : 'Offline'"></span>
              </div>
            </div>
          </div>

          <!-- Lock Status Bar -->
          <div x-show="lock.locked" x-transition class="flex items-center justify-between px-4 py-2 rounded-xl"
               :class="lock.locked_by_me ? 'bg-slt-accent/20 border border-slt-accent/30' : 'bg-red-500/20 border border-red-500/30'">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" :class="lock.locked_by_me ? 'text-slt-accent' : 'text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <span class="text-sm font-medium" :class="lock.locked_by_me ? 'text-slt-accent' : 'text-red-400'">
                <template x-if="lock.locked_by_me">
                  <span>You have locked this chat. Other admins cannot reply.</span>
                </template>
                <template x-if="!lock.locked_by_me">
                  <span>Chat locked by <strong x-text="lock.locked_by"></strong>. You cannot reply.</span>
                </template>
              </span>
            </div>
            <button x-show="lock.locked_by_me" @click="releaseLock()"
                    class="px-3 py-1 rounded-lg bg-white/10 text-slt-accent hover:bg-white/20 text-sm transition-all">
              Release Lock
            </button>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="flex-1 p-4 space-y-3 overflow-y-auto bg-slt-ink/50 scrollbar-dark" x-ref="scroll" style="min-height: 400px; max-height: calc(100vh - 400px);">
          <!-- Date Separator -->
          <div class="flex items-center justify-center my-4" x-show="messages.length > 0">
            <span class="px-3 py-1 rounded-full bg-white/10 text-xs text-slt-muted">Today</span>
          </div>

          <template x-for="m in messages" :key="m.id + '_' + m.uuid">
            <div class="flex" :class="m.direction === 'out' ? 'justify-end' : 'justify-start'">
              <div class="max-w-[75%] rounded-2xl px-4 py-2"
                   :class="m.direction === 'out'
                        ? 'bg-slt-primary text-white'
                        : 'bg-white/10 text-white'">
                <div class="whitespace-pre-wrap" x-text="m.body"></div>
                <div class="flex items-center justify-end gap-1 mt-1">
                  <span class="text-[11px] opacity-70" x-text="formatTime(m.sent_at)"></span>
                  <template x-if="m.direction === 'out'">
                    <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                  </template>
                </div>
              </div>
            </div>
          </template>

          <div class="text-sm text-slt-muted text-center py-8" x-show="messages.length===0">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p>No messages yet</p>
            <p class="text-xs mt-1">Click Sync to fetch messages or send a WhatsApp message to this number.</p>
          </div>
        </div>

        <!-- Message Input Area -->
        <div class="p-4 border-t border-white/10 bg-white/5">
          <!-- Locked by other admin warning -->
          <div x-show="lock.locked && !lock.locked_by_me" class="mb-3 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>This chat is locked by <strong x-text="lock.locked_by"></strong>. Wait for them to release it or the lock will expire in 30 minutes.</span>
          </div>

          <form class="flex gap-3" @submit.prevent="send()">
            <div class="flex-1 relative">
              <input
                x-model="draft"
                placeholder="Type a message..."
                :disabled="lock.locked && !lock.locked_by_me"
                class="w-full rounded-2xl bg-white/5 border-white/10 text-white placeholder-slt-muted pr-12 focus:border-slt-primary focus:ring-slt-primary disabled:opacity-50 disabled:cursor-not-allowed"
              />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slt-muted hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
            </div>
            <button
              type="submit"
              class="px-6 py-2 rounded-2xl bg-slt-accent text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transition-all"
              :disabled="sending || !draft.trim() || (lock.locked && !lock.locked_by_me)">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              <span x-show="!sending">Send</span>
              <span x-show="sending">Sending...</span>
            </button>
          </form>
          <div class="flex items-center gap-2 mt-2 text-xs text-slt-muted">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Press Enter to send. Auto-syncing with WhatsApp every 5 seconds.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Save Contact Modal -->
  <dialog id="saveContactModal" class="rounded-2xl p-0 w-full max-w-lg backdrop:bg-black/50">
    <form method="POST" action="{{ route('contacts.update', $contact) }}" class="bg-slt-ink rounded-2xl overflow-hidden">
      @csrf
      @method('PUT')
      <div class="gradient-header p-4 text-white">
        <h3 class="font-semibold text-lg">Save Contact</h3>
        <p class="text-sm text-white/80 mt-0.5">Add a name for this number</p>
      </div>
      <div class="p-6 space-y-4">
        <div>
          <label class="text-sm text-slt-muted block mb-2">Name</label>
          <input name="name" value="{{ $contact->name }}" placeholder="John Doe"
            class="w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-slt-muted focus:border-slt-primary focus:ring-slt-primary" />
        </div>
        <div>
          <label class="text-sm text-slt-muted block mb-2">Mobile Number</label>
          <input name="mobile" value="{{ $contact->mobile }}" readonly
            class="w-full rounded-xl bg-white/5 border-white/10 text-slt-muted" />
        </div>
      </div>
      <div class="p-4 border-t border-white/10 flex justify-end gap-3">
        <button type="button" onclick="document.getElementById('saveContactModal').close()"
          class="px-4 py-2 rounded-xl border border-white/10 text-slt-muted hover:text-white hover:bg-white/5 transition-all">
          Cancel
        </button>
        <button type="submit"
          class="px-6 py-2 rounded-xl bg-slt-primary text-white hover:bg-slt-primary-600 transition-all">
          Save Contact
        </button>
      </div>
    </form>
  </dialog>

  <script>
    function chatApp(contactId) {
      return {
        sidebarOpen: false,
        online: true,
        sending: false,
        draft: '',
        messages: [],
        lock: { locked: false, locked_by: null, locked_by_me: false },
        lockNotice: '',
        pollMs: 5000,
        syncEveryMs: 5000,
        syncContactsEveryMs: 30000,
        syncContactsLimit: 10,
        lastSyncAt: 0,
        lastContactSyncAt: 0,
        syncing: false,
        syncingAll: false,
        syncingContacts: false,
        listLoading: false,
        readMarking: false,
        timer: null,
        lockTimer: null,
        lastLockedBy: null,

        openSaveContact() {
          document.getElementById('saveContactModal').showModal();
        },

        async init() {
          await this.fetchLockStatus();
          await this.syncNow();
          await this.load();
          this.timer = setInterval(() => this.load(), this.pollMs);
          this.lockTimer = setInterval(() => this.refreshLockIfMine(), 15000);

          // Best-effort release when leaving page
          window.addEventListener('beforeunload', () => {
            if (this.lock.locked_by_me) {
              try {
                fetch(`/chats/${contactId}/lock/release`, {
                  method: 'POST',
                  keepalive: true,
                  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
              } catch (e) {}
            }
          });
        },

        async fetchLockStatus() {
          try {
            const res = await fetch(`/chats/${contactId}/lock`);
            const data = await res.json();

            this.lock = {
              locked: !!data.locked,
              locked_by: data.locked_by || null,
              locked_by_me: !!data.locked_by_me,
            };
          } catch (e) {
            console.error('Failed to fetch lock status', e);
          }
        },

        async refreshLockIfMine() {
          // Only refresh if we own the lock
          if (this.lock.locked_by_me) {
            await this.acquireLock();
          } else {
            await this.fetchLockStatus();
          }
        },

        async acquireLock() {
          try {
            const res = await fetch(`/chats/${contactId}/lock/acquire`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();

            this.lock = {
              locked: !!data.locked,
              locked_by: data.locked_by || null,
              locked_by_me: !!data.locked_by_me,
            };
          } catch (e) {
            console.error('Failed to acquire lock', e);
          }
        },

        async releaseLock() {
          try {
            await fetch(`/chats/${contactId}/lock/release`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            await this.fetchLockStatus();
          } catch (e) {
            console.error('Failed to release lock', e);
          }
        },

        async manualSync() {
          await this.syncNow();
          await this.load();
        },

        async syncAllNow() {
          if (this.syncingAll) return;
          this.syncingAll = true;
          try {
            await fetch('/chats/sync-all', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
          } finally {
            this.lastSyncAt = Date.now();
            this.syncingAll = false;
          }
        },

        async syncRecentContactsIfDue() {
          const now = Date.now();
          if (this.syncingContacts || (now - this.lastContactSyncAt) < this.syncContactsEveryMs) return;
          this.syncingContacts = true;
          try {
            const body = new URLSearchParams();
            body.set('limit', String(this.syncContactsLimit));
            await fetch('/contacts/sync-recent', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body,
            });
          } catch (e) {
            // best-effort contact sync
          } finally {
            this.lastContactSyncAt = Date.now();
            this.syncingContacts = false;
          }
        },

        async refreshList() {
          if (this.listLoading) return;
          const listEl = this.$refs.chatList;
          if (!listEl) return;
          this.listLoading = true;
          try {
            await this.syncRecentContactsIfDue();
            const params = new URLSearchParams({
              active_contact_id: contactId,
              show_lock: '1',
              show_active: '1'
            });
            const res = await fetch(`/chats/list?${params.toString()}`);
            if (!res.ok) return;
            listEl.innerHTML = await res.text();
          } catch (e) {
            // best-effort refresh
          } finally {
            this.listLoading = false;
          }
        },

        async markRead() {
          if (this.readMarking) return;
          this.readMarking = true;
          try {
            await fetch(`/chats/${contactId}/read`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
          } finally {
            this.readMarking = false;
          }
        },

        async syncNow() {
          if (this.syncing) return;
          this.syncing = true;
          try {
            await fetch(`/chats/${contactId}/sync`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
          } finally {
            this.lastSyncAt = Date.now();
            this.syncing = false;
          }
        },

        async autoSyncIfDue() {
          const now = Date.now();
          if ((now - this.lastSyncAt) < this.syncEveryMs) return;
          await this.syncAllNow();
        },

        async load() {
          try {
            await this.fetchLockStatus();
            await this.autoSyncIfDue();
            const res = await fetch(`/chats/${contactId}/messages?limit=0`);
            const data = await res.json();
            this.messages = data.messages || [];
            this.$nextTick(() => this.scrollToBottom());
            this.online = true;
            await this.markRead();
            this.refreshList();
          } catch (e) {
            this.online = false;
          }
        },

        async send() {
          if (this.lock.locked && !this.lock.locked_by_me) return;
          if (!this.draft.trim()) return;
          this.sending = true;
          try {
            const res = await fetch(`/chats/${contactId}/send`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ message: this.draft })
            });

            const data = await res.json();
            if (!res.ok) {
              if (res.status === 423) {
                await this.fetchLockStatus();
              }
              alert(data.error || 'Send failed');
            }

            this.draft = '';
            await this.load();
          } finally {
            this.sending = false;
          }
        },

        scrollToBottom() {
          const el = this.$refs.scroll;
          el.scrollTop = el.scrollHeight;
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
