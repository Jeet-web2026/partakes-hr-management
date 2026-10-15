@props([
'agreementId' => ''
])
<button
    type="button"
    class="agreement-history-btn text-white/50 hover:text-white/90 transition-colors"
    data-id="{{ $agreementId }}" id="agreementHistoryBtn">
    <i class="bi bi-grid text-base"></i>
</button>

{{-- Slide-over panel — include this ONCE per page (e.g. in your layout) --}}
<div id="history-panel-backdrop" class="hidden fixed inset-0 bg-black/50 z-40 transition-opacity"></div>

<div id="history-panel" class="hidden fixed top-0 right-0 h-full w-full sm:w-96 bg-slate-900 border-l border-white/10 z-50 shadow-xl transform translate-x-full transition-transform duration-300">
    <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
        <h3 class="text-sm font-semibold text-white">Agreement History</h3>
        <button id="history-panel-close" class="text-white/50 hover:text-white/90">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="history-panel-content" class="p-4 overflow-y-auto h-[calc(100%-52px)] space-y-3">
        {{-- populated via JS --}}
    </div>
</div>