@props(['page' => null])

<a href="/feedback{{ $page ? '?pageId=' . urlencode($page) : '' }}"
    title="Give feedback on this page" aria-label="Give feedback on this page"
    class="group fixed bottom-4 right-4 z-40 inline-flex h-12 items-center overflow-hidden rounded-full bg-blue-600 text-white shadow-lg ring-1 ring-white/10 transition-colors hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">

    {{-- label: hidden until hover or keyboard focus --}}
    <span class="max-w-0 overflow-hidden whitespace-nowrap text-sm font-medium opacity-0 transition-all duration-300 ease-out motion-reduce:transition-none group-hover:max-w-[10rem] group-hover:pl-4 group-hover:opacity-100 group-focus-visible:max-w-[10rem] group-focus-visible:pl-4 group-focus-visible:opacity-100">
        Give feedback
    </span>

    {{-- icon stays pinned on the right --}}
    <span class="flex h-12 w-12 shrink-0 items-center justify-center">
        <i class="bi bi-chat-left-dots text-lg"></i>
    </span>
</a>