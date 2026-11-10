@props(['page' => null])

<div
    x-data="{
        open: false,
        src: '',
        show() { this.src = '/feedback?embed=1&pageId={{ $page }}'; this.open = true; },
        hide() { this.open = false; this.src = ''; }
    }"
    x-on:message.window="if ($event.origin === window.location.origin && $event.data?.type === 'feedback-close') hide()"
    x-on:keydown.escape.window="open && hide()">

    <button
        type="button"
        @click="show()"
        title="Give feedback on this page" aria-label="Give feedback on this page"
        class="inline-flex h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">
        <i class="bi bi-chat-left-dots text-lg"></i>
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4"
            @click.self="hide()">

            <div class="relative h-[100vh] w-full max-w-7xl overflow-hidden rounded-xl border border-white/10 bg-card shadow-xl">
                <button
                    type="button"
                    @click="hide()"
                    aria-label="Close"
                    class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full hover:bg-white/10">
                    <i class="bi bi-x-lg"></i>
                </button>

                <iframe
                    :src="src || 'about:blank'"
                    title="Feedback"
                    class="h-full w-full border-0"></iframe>
            </div>
        </div>
    </template>
</div>