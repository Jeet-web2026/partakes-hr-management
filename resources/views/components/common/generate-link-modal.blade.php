<div id="generate-link-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60">
    <div class="rounded-lg border border-white/10 bg-card p-6 shadow-xl" style="width: 500px;">
        <div class="flex items-start justify-between mb-2">
            <div class="flex items-center gap-2">
                <i class="ri-links-line text-blue-400 text-xl"></i>
                <h3 class="text-lg font-semibold">Generate Link</h3>
            </div>
            <button id="close-generate-link-modal" class="text-muted-foreground hover:text-white">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <p class="text-sm text-muted-foreground mb-4">
            Generate a unique, single-use link to showcase someone to show their agreement/draft document information. The link will expire after 24 hours or once it has been used.
        </p>

        <button id="generate-link-btn-action"
            class="w-full mb-4 inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-medium">
            <i class="ri-magic-line"></i>
            Generate Link
        </button>

        <div id="generated-link-wrapper" class="hidden">
            <label class="block text-xs text-muted-foreground mb-1">Generated Link:</label>
            <div class="flex items-center gap-2">
                <input id="generated-link-input" type="text" readonly
                    class="flex-1 rounded-md border border-white/10 bg-black/20 px-3 py-2 text-sm truncate" />
                <button id="copy-generated-link"
                    class="inline-flex items-center gap-1 rounded-md border border-white/10 px-3 py-2 text-sm hover:bg-white/5">
                    <i class="ri-file-copy-line"></i> Copy
                </button>
            </div>
        </div>
    </div>
</div>