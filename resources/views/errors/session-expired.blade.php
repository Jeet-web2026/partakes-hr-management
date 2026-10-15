<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-slate-950 text-slate-100 px-4">

    <div class="w-full max-w-md text-center border px-4 py-8 rounded-xl shadow-lg border-gray-700">

        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10 border border-red-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="text-red-400">
                <path d="M12 9v4" />
                <path d="M12 17h.01" />
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
            </svg>
        </div>

        <h1 class="text-xl font-semibold text-white mb-2">
            Session Expired
        </h1>

        <p class="text-sm text-slate-400 mb-8 leading-relaxed">
            Your session has expired or you're not signed in. Please log in
            again to continue where you left off.
        </p>

        <a href="https://partakers.in/dashboard"
            class="inline-flex items-center justify-center gap-2 rounded-md bg-cyan-500
            hover:bg-cyan-400 transition-colors px-5 py-2.5 text-sm font-medium
            text-slate-950">

            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                <polyline points="10 17 15 12 10 7" />
                <line x1="15" y1="12" x2="3" y2="12" />
            </svg>

            Go to Home
        </a>

    </div>

</body>

</html>