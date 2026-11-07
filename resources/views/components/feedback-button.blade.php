@props(['page' => null])

<a href="/feedback?pageId={{ $page }}"
    title="Give feedback on this page" aria-label="Give feedback on this page"
    class="inline-flex h-9 w-9 sm:w-auto sm:px-3 items-center justify-center gap-2 rounded-md border border-white/10 hover:bg-white/5 transition">
    <i class="bi bi-chat-left-dots text-lg"></i>
</a>