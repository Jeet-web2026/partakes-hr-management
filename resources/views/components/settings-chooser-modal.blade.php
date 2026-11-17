<dialog id="settingsChooser"
    class="m-auto w-[92%] max-w-3xl max-h-120 h-100 rounded-xl border border-white/10 bg-card p-6 text-white shadow-2xl backdrop:bg-black/60"
    onclick="if (event.target === this) this.close()">

    <div class="mb-5 flex items-start justify-between">
        <div>
            <h2 class="text-lg font-semibold">Choose settings</h2>
            <p class="text-xs text-muted-foreground">Which application do you want to configure?</p>
        </div>
        <button type="button" onclick="this.closest('dialog').close()"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-white/10 transition"
            aria-label="Close">
            <i class="ri-close-line text-lg"></i>
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 h-[80%]">
        <a href="{{ \Filament\Facades\Filament::getPanel('settings')->getUrl() }}"
            class="flex flex-col items-center gap-2 justify-center rounded-xl border border-white/10 p-6 text-center transition hover:border-primary hover:bg-white/5">
            <i class="ri-team-line text-5xl text-primary"></i>
            <span class="font-semibold text-xl">HR Settings</span>
            <span class="text-xs text-muted-foreground">Offices, designations, interview steps</span>
        </a>

        <a href="{{ route('contact-app.settings') }}"
            class="flex flex-col items-center gap-2 justify-center rounded-xl border border-white/10 p-6 text-center transition hover:border-primary hover:bg-white/5">
            <i class="ri-contacts-book-line text-5xl text-primary"></i>
            <span class="font-semibold text-xl">Contact App Settings</span>
            <span class="text-xs text-muted-foreground">Contact application configuration</span>
        </a>
    </div>
</dialog>