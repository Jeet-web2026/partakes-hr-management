<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Expired</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Figtree", sans-serif; }
        body {
            background: #0b1220;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            max-width: 480px;
            width: 100%;
            background: rgba(153, 27, 27, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
        }
        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #f87171;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-circle svg { width: 26px; height: 26px; stroke: #1a0b0b; }
        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #f87171;
            margin-bottom: 12px;
        }
        .subtitle {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 24px;
        }
        .info-box {
            background: rgba(153, 27, 27, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 8px;
            padding: 16px;
            text-align: left;
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .info-box svg { width: 18px; height: 18px; stroke: #f87171; flex-shrink: 0; margin-top: 2px; }
        .info-box .info-title { font-size: 14px; font-weight: 600; color: #f87171; margin-bottom: 4px; }
        .info-box .info-text { font-size: 13px; color: #cbd5e1; line-height: 1.5; }
        .footer-text { font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <h1>Link has expired</h1>
        <p class="subtitle">
            This link expired on {{ $expiredAt ?? now()->format('M j, Y \a\t g:i A') }}.
        </p>
        <div class="info-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <div class="info-title">Why did this expire?</div>
                <div class="info-text">Share links are valid for a limited time from when they were created, for security purposes.</div>
            </div>
        </div>
        <p class="footer-text">Need a new link? Please contact the person who originally shared this with you.</p>
    </div>
</body>
</html>