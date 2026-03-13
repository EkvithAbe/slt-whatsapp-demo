<x-app-layout>
  <div class="max-w-7xl mx-auto p-3 sm:p-4 chat-shell-height min-h-0">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full min-h-0">
      <!-- Chat List Sidebar -->
      <div class="lg:col-span-1 rounded-2xl bg-white/5 border border-white/10 overflow-hidden flex flex-col h-full min-h-0">
        <div class="p-4 border-b border-white/10 flex items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-slt-accent"></span>
            <span class="font-semibold text-white">Chats</span>
          </div>

          <span class="text-xs text-slt-muted">Latest chats</span>
        </div>

        <!-- Sync Inbox Button -->
        <div class="p-3 border-b border-white/10">
          <form method="POST" action="{{ route('contacts.syncRecent') }}" class="w-full">
            @csrf
            <input type="hidden" name="limit" value="{{ (int) config('chat.sync_recent_limit', 40) }}" />
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
        <div id="chatList" class="chat-list-scroll divide-y divide-white/5 flex-1 min-h-0 overflow-y-auto scrollbar-dark touch-pan-y">
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
      <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-6 sm:p-8 flex flex-col items-center justify-center h-full min-h-0">
        <div class="w-20 h-20 rounded-full bg-slt-info/20 flex items-center justify-center mb-6">
          <svg class="w-10 h-10 text-slt-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <h3 class="text-lg sm:text-xl font-semibold text-white mb-2 text-center">Select a conversation</h3>
        <p class="text-slt-muted text-center max-w-sm">
          Choose a contact from the list to view your conversation history and send messages.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-slt-muted bg-white/5 px-4 py-2 rounded-xl text-center">
          <svg class="w-4 h-4 text-slt-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Messages auto-sync every 5 seconds. New chats auto-sync every 30 seconds.
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const listEl = document.getElementById('chatList');
      if (!listEl) return;

      const csrf = '{{ csrf_token() }}';
      let refreshing = false;
      let syncingContacts = false;
      let lastContactSyncAt = 0;
      let pauseListRefreshUntil = 0;
      const listLimit = {{ (int) $listLimit }};
      const syncContactsEveryMs = 30000;
      const syncContactsLimit = {{ (int) config('chat.sync_recent_limit', 40) }};

      const showError = (message, key = 'general') => {
        const text = String(message || '').trim();
        if (!text) return;

        window.dispatchEvent(new CustomEvent('app:error', {
          detail: { key, message: text }
        }));
      };

      const clearError = (key = 'general') => {
        window.dispatchEvent(new CustomEvent('app:error:clear', {
          detail: { key }
        }));
      };

      const pauseRefresh = () => {
        pauseListRefreshUntil = Date.now() + 2500;
      };

      const forceListWheelScroll = (event) => {
        if (listEl.scrollHeight <= listEl.clientHeight) return;
        const before = listEl.scrollTop;
        listEl.scrollTop += event.deltaY;
        if (listEl.scrollTop !== before) {
          event.preventDefault();
        }
        pauseRefresh();
      };

      listEl.addEventListener('pointerdown', pauseRefresh, { passive: true });
      listEl.addEventListener('touchstart', pauseRefresh, { passive: true });
      listEl.addEventListener('wheel', forceListWheelScroll, { passive: false });
      listEl.addEventListener('scroll', pauseRefresh, { passive: true });

      const syncRecentContactsIfDue = async () => {
        const now = Date.now();
        if (syncingContacts || (now - lastContactSyncAt) < syncContactsEveryMs) return;
        syncingContacts = true;
        try {
          const body = new URLSearchParams();
          body.set('limit', String(syncContactsLimit));
          const res = await fetch('/contacts/sync-recent', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body,
          });
          if (!res.ok) {
            showError('Failed to sync recent contacts.', 'sync-contacts');
            return;
          }
          clearError('sync-contacts');
        } catch (e) {
          showError('Failed to sync recent contacts.', 'sync-contacts');
        } finally {
          lastContactSyncAt = Date.now();
          syncingContacts = false;
        }
      };

      const refreshList = async () => {
        if (refreshing) return;
        if (Date.now() < pauseListRefreshUntil) return;
        refreshing = true;
        try {
          await syncRecentContactsIfDue();
          const params = new URLSearchParams({
            show_preview: '1',
            limit: String(listLimit),
          });
          const res = await fetch(`/chats/list?${params.toString()}`);
          if (!res.ok) {
            showError('Failed to refresh chat list.', 'refresh-list');
            return;
          }
          const nextHtml = await res.text();
          if (Date.now() < pauseListRefreshUntil) {
            clearError('refresh-list');
            return;
          }
          if (nextHtml === listEl.innerHTML) {
            clearError('refresh-list');
            return;
          }
          const previousScrollTop = listEl.scrollTop;
          const wasNearBottom =
            (listEl.scrollHeight - (listEl.scrollTop + listEl.clientHeight)) < 24;
          listEl.innerHTML = nextHtml;
          listEl.scrollTop = wasNearBottom ? listEl.scrollHeight : previousScrollTop;
          clearError('refresh-list');
        } catch (e) {
          showError('Failed to refresh chat list.', 'refresh-list');
        } finally {
          refreshing = false;
        }
      };

      refreshList();
      setInterval(refreshList, 5000);
    })();
  </script>
</x-app-layout>
