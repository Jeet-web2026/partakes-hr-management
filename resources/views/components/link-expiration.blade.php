@props([
'expiredAt' => ''
])
<div class="rounded-md border border-blue-500/60 bg-[#071d3a] px-4 py-4 sm:px-5">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <!-- Left Content -->
        <div class="flex items-start gap-3">

            <i class="bi bi-stopwatch mt-0.5 shrink-0 text-2xl text-blue-400"></i>

            <div>
                <div class="text-sm font-medium text-blue-400">
                    Link Expires In
                </div>

                <div class="text-sm text-blue-300">
                    This invitation link will expire automatically for security.
                </div>
            </div>

        </div>

        <!-- Countdown -->
        <div class="text-left sm:text-right">

            <div class="flex items-center gap-1.5">
                <span
                    id="countdown-hours"
                    class="rounded-md bg-blue-600/40 px-3 py-1.5 text-sm font-medium text-blue-200">
                    00h
                </span>

                <span
                    id="countdown-minutes"
                    class="rounded-md bg-blue-600/40 px-3 py-1.5 text-sm font-medium text-blue-200">
                    00m
                </span>

                <span
                    id="countdown-seconds"
                    class="rounded-md bg-blue-600/40 px-3 py-1.5 text-sm font-medium text-blue-200">
                    00s
                </span>
            </div>

            <div class="mt-1 text-xs text-blue-400">
                {{ $expiredAt ? \Carbon\Carbon::parse($expiredAt)->format('n/j/Y \a\t g:i:s A') : '' }}
            </div>

        </div>

    </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function () {

        const expiredAt = @json($expiredAt ?? null);

        const hoursEl = document.getElementById('countdown-hours');
        const minutesEl = document.getElementById('countdown-minutes');
        const secondsEl = document.getElementById('countdown-seconds');

        if (!expiredAt || !hoursEl || !minutesEl || !secondsEl) {
            return;
        }

        const expiryDate = new Date(
            expiredAt.replace(' ', 'T')
        );

        const expiryTime = expiryDate.getTime();

        if (isNaN(expiryTime)) {
            console.error('Invalid expiration date:', expiredAt);
            return;
        }

        function updateCountdown() {

            const now = Date.now();
            const difference = expiryTime - now;

            if (difference <= 0) {

                hoursEl.textContent = '00h';
                minutesEl.textContent = '00m';
                secondsEl.textContent = '00s';

                clearInterval(countdownInterval);

                return;
            }

            const totalSeconds = Math.floor(difference / 1000);

            const hours = Math.floor(totalSeconds / 3600);

            const minutes = Math.floor(
                (totalSeconds % 3600) / 60
            );

            const seconds = totalSeconds % 60;

            hoursEl.textContent =
                String(hours).padStart(2, '0') + 'h';

            minutesEl.textContent =
                String(minutes).padStart(2, '0') + 'm';

            secondsEl.textContent =
                String(seconds).padStart(2, '0') + 's';
        }

        updateCountdown();

        const countdownInterval = setInterval(
            updateCountdown,
            1000
        );

    });
</script>