<x-app-layout>
  <div class="max-w-7xl mx-auto p-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Chat List Sidebar -->
      <div class="lg:col-span-1 rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
        <div class="p-4 border-b border-white/10 flex items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-slt-accent"></span>
            <span class="font-semibold text-white">Chats</span>
          </div>

          <button
            onclick="document.getElementById('addContact').showModal()"
            class="px-4 py-2 rounded-xl bg-slt-accent text-white hover:opacity-90 flex items-center gap-2 text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Contact
          </button>
        </div>

        <!-- Sync Inbox Button -->
        <div class="p-3 border-b border-white/10">
          <form method="POST" action="{{ route('contacts.syncRecent') }}" class="w-full">
            @csrf
            <input type="hidden" name="limit" value="10" />
            <button type="submit"
              class="w-full px-4 py-2.5 rounded-xl border border-white/10 text-slt-muted hover:text-white hover:bg-white/5 flex items-center justify-center gap-2 text-sm transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Sync Inbox (Find New Chats)
            </button>
          </form>
        </div>

        <!-- Contact List -->
        <div id="chatList" class="divide-y divide-white/5 max-h-[calc(100vh-280px)] overflow-y-auto scrollbar-dark">
          @include('chats.partials.list-items', [
            'contacts' => $contacts,
            'showPreview' => true,
            'showLock' => false,
            'showActive' => false,
            'activeContactId' => null,
          ])
        </div>
      </div>

      <!-- Empty State / Welcome Panel -->
      <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-8 flex flex-col items-center justify-center min-h-[500px]">
        <div class="w-20 h-20 rounded-full bg-slt-info/20 flex items-center justify-center mb-6">
          <svg class="w-10 h-10 text-slt-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Select a conversation</h3>
        <p class="text-slt-muted text-center max-w-sm">
          Choose a contact from the list to view your conversation history and send messages.
        </p>
        <div class="mt-6 flex items-center gap-2 text-sm text-slt-muted bg-white/5 px-4 py-2 rounded-full">
          <svg class="w-4 h-4 text-slt-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Messages auto-sync every 5 seconds. New chats auto-sync every 30 seconds.
        </div>
      </div>
    </div>
  </div>

  <!-- Add Contact Modal -->
  <dialog id="addContact" class="rounded-2xl p-0 w-full max-w-lg backdrop:bg-black/50">
    <form method="POST" action="{{ route('contacts.store') }}" class="bg-slt-ink rounded-2xl overflow-hidden">
      @csrf
      <div class="gradient-header p-4 text-white">
        <h3 class="font-semibold text-lg">Add New Contact</h3>
        <p class="text-sm text-white/80 mt-0.5">Add a name for this number</p>
      </div>
      <div class="p-6 space-y-4">
        <div>
          <label class="text-sm text-slt-muted block mb-2">Name</label>
          <input name="name" placeholder="John Doe"
            class="w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-slt-muted focus:border-slt-primary focus:ring-slt-primary" />
        </div>
        <div>
          <label class="text-sm text-slt-muted block mb-2">Mobile Number</label>
          <input name="mobile" required placeholder="94771234567"
            class="w-full rounded-xl bg-white/5 border-white/10 text-white placeholder-slt-muted focus:border-slt-primary focus:ring-slt-primary" />
        </div>
      </div>
      <div class="p-4 border-t border-white/10 flex justify-end gap-3">
        <button type="button" onclick="document.getElementById('addContact').close()"
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
    (function () {
      const listEl = document.getElementById('chatList');
      if (!listEl) return;

      const csrf = '{{ csrf_token() }}';
      let refreshing = false;
      let syncing = false;
      let lastSyncAt = 0;
      let syncingContacts = false;
      let lastContactSyncAt = 0;
      const syncEveryMs = 5000;
      const syncContactsEveryMs = 30000;
      const syncContactsLimit = 10;

      const syncAllIfDue = async () => {
        const now = Date.now();
        if (syncing || (now - lastSyncAt) < syncEveryMs) return;
        syncing = true;
        try {
          await fetch('/chats/sync-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf }
          });
        } catch (e) {
          // best-effort sync
        } finally {
          lastSyncAt = Date.now();
          syncing = false;
        }
      };

      const syncRecentContactsIfDue = async () => {
        const now = Date.now();
        if (syncingContacts || (now - lastContactSyncAt) < syncContactsEveryMs) return;
        syncingContacts = true;
        try {
          const body = new URLSearchParams();
          body.set('limit', String(syncContactsLimit));
          await fetch('/contacts/sync-recent', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body,
          });
        } catch (e) {
          // best-effort contact sync
        } finally {
          lastContactSyncAt = Date.now();
          syncingContacts = false;
        }
      };

      const refreshList = async () => {
        if (refreshing) return;
        refreshing = true;
        try {
          await syncRecentContactsIfDue();
          await syncAllIfDue();
          const res = await fetch('/chats/list?show_preview=1');
          if (!res.ok) return;
          listEl.innerHTML = await res.text();
        } catch (e) {
          // best-effort refresh
        } finally {
          refreshing = false;
        }
      };

      refreshList();
      setInterval(refreshList, 5000);
    })();
  </script>
</x-app-layout>
