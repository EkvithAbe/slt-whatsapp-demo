<x-app-layout>
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="rounded-2xl bg-glass-bg backdrop-blur-xl overflow-hidden border border-glass-border shadow-xl">
      <div class="p-4 border-b border-glass-border flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slt-blue to-slt-green flex items-center justify-center shadow-lg">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <h2 class="font-semibold text-text-primary text-lg">API Logs</h2>
            <p class="text-sm text-text-secondary">SLT WhatsApp API call history</p>
          </div>
        </div>
        <div class="text-sm text-text-secondary">
          {{ $logs->total() }} total records
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-dark-bg/50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Time</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Endpoint</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Duration</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Details</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-glass-border">
            @forelse($logs as $log)
              <tr class="hover:bg-dark-card/50 transition-colors">
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm text-text-primary">{{ $log->created_at->format('H:i:s') }}</div>
                  <div class="text-xs text-text-secondary">{{ $log->created_at->format('Y-m-d') }}</div>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-lg text-xs font-medium bg-dark-card text-text-secondary">
                      {{ $log->method ?? 'POST' }}
                    </span>
                    <span class="text-sm font-mono text-slt-blue">{{ $log->endpoint }}</span>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  @if(($log->status ?? 0) < 300)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slt-green/20 text-slt-green border border-slt-green/30">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                      {{ $log->status }}
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-error-red/20 text-error-red border border-error-red/30">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                      {{ $log->status }}
                    </span>
                  @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <div class="h-1.5 w-16 rounded-full bg-dark-card overflow-hidden">
                      <div class="h-full rounded-full {{ $log->duration_ms < 500 ? 'bg-slt-green' : ($log->duration_ms < 1000 ? 'bg-yellow-500' : 'bg-error-red') }}"
                           style="width: {{ min(100, $log->duration_ms / 20) }}%"></div>
                    </div>
                    <span class="text-sm text-text-secondary">{{ $log->duration_ms }}ms</span>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <details class="group">
                    <summary class="cursor-pointer text-sm text-slt-blue hover:text-slt-blue/80 flex items-center gap-1 transition-colors">
                      <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                      View Request
                    </summary>
                    <div class="mt-3 p-4 bg-dark-bg rounded-xl text-xs font-mono text-text-secondary overflow-auto max-h-48 border border-glass-border">
                      <pre class="whitespace-pre-wrap">{{ json_encode($log->request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                  </details>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-4 py-12 text-center">
                  <div class="w-16 h-16 mx-auto rounded-full bg-dark-card flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div class="text-text-secondary font-medium">No API logs yet</div>
                  <div class="text-sm text-text-secondary mt-1">Logs will appear here when API calls are made</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($logs->hasPages())
        <div class="p-4 border-t border-glass-border bg-dark-bg/30">
          <div class="flex items-center justify-between">
            <div class="text-sm text-text-secondary">
              Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
            </div>
            <div class="flex gap-2">
              @if($logs->onFirstPage())
                <span class="px-4 py-2 rounded-xl bg-dark-card/30 text-text-secondary cursor-not-allowed">Previous</span>
              @else
                <a href="{{ $logs->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-dark-card text-text-primary hover:bg-dark-card/80 transition-colors">Previous</a>
              @endif

              @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-slt-blue to-slt-green text-white hover:opacity-90 transition-opacity">Next</a>
              @else
                <span class="px-4 py-2 rounded-xl bg-dark-card/30 text-text-secondary cursor-not-allowed">Next</span>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</x-app-layout>
