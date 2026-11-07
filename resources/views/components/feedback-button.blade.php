@props(['page' => null])

<a href="/feedback?pageId={{ $page }}"
    title="Give feedback on this page" aria-label="Give feedback on this page"
    class="fixed bottom-4 right-4 z-40 inline-flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg ring-1 ring-white/10 transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
    <i class="bi bi-chat-dots text-sm"></i>
</a>