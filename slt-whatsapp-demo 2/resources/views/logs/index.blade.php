<x-app-layout>
  <div class="max-w-7xl mx-auto p-4">
    <div class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
      <!-- Header -->
      <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-slt-primary/20 flex items-center justify-center">
            <svg class="w-6 h-6 text-slt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-semibold text-white">API Logs</h1>
            <p class="text-sm text-slt-muted">SLT WhatsApp API call history</p>
          </div>
        </div>
        <div class="text-sm text-slt-muted">
          {{ $logs->total() }} total records
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-white/5">
            <tr>
              <th class="p-4 text-left text-xs font-medium text-slt-muted uppercase tracking-wider">Time</th>
              <th class="p-4 text-left text-xs font-medium text-slt-muted uppercase tracking-wider">Endpoint</th>
              <th class="p-4 text-left text-xs font-medium text-slt-muted uppercase tracking-wider">Status</th>
              <th class="p-4 text-left text-xs font-medium text-slt-muted uppercase tracking-wider">Duration</th>
              <th class="p-4 text-left text-xs font-medium text-slt-muted uppercase tracking-wider">Details</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            @foreach($logs as $log)
              <tr class="hover:bg-white/5 transition-colors">
                <td class="p-4">
                  <div class="text-white font-medium">{{ $log->created_at->format('H:i:s') }}</div>
                  <div class="text-xs text-slt-muted">{{ $log->created_at->format('Y-m-d') }}</div>
                </td>
                <td class="p-4">
                  <span class="text-xs px-2 py-1 rounded bg-white/10 text-slt-muted mr-2">
                    {{ $log->method ?? 'POST' }}
                  </span>
                  <span class="text-slt-info font-medium">{{ $log->endpoint }}</span>
                </td>
                <td class="p-4">
                  @if(($log->status ?? 0) < 300)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slt-accent/20 text-slt-accent text-sm">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      {{ $log->status }}
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-500/20 text-red-400 text-sm">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                      {{ $log->status }}
                    </span>
                  @endif
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-2">
                    <div class="w-16 h-1.5 rounded-full bg-white/10 overflow-hidden">
                      <div class="h-full rounded-full {{ ($log->duration_ms ?? 0) < 500 ? 'bg-slt-accent' : (($log->duration_ms ?? 0) < 1000 ? 'bg-yellow-500' : 'bg-red-500') }}"
                           style="width: {{ min(100, ($log->duration_ms ?? 0) / 10) }}%"></div>
                    </div>
                    <span class="text-slt-muted text-sm">{{ $log->duration_ms }}ms</span>
                  </div>
                </td>
                <td class="p-4">
                  <details class="group">
                    <summary class="cursor-pointer text-slt-info hover:text-slt-info/80 flex items-center gap-1 text-sm">
                      <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                      View Request
                    </summary>
                    <pre class="mt-3 p-4 bg-slt-ink rounded-xl text-xs text-slt-muted overflow-auto max-w-lg border border-white/10">{{ json_encode($log->request, JSON_PRETTY_PRINT) }}</pre>
                  </details>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t border-white/10">
        {{ $logs->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
