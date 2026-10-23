<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Figtree", sans-serif;
        }

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
            background: rgba(22, 101, 52, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.4);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
        }

        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-circle svg {
            width: 26px;
            height: 26px;
            stroke: #052e16;
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #4ade80;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 24px;
        }

        .info-box {
            background: rgba(22, 101, 52, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.35);
            border-radius: 8px;
            padding: 16px;
            text-align: left;
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        .info-box.warning {
            background: rgba(161, 98, 7, 0.15);
            border-color: rgba(245, 158, 11, 0.35);
            margin-bottom: 20px;
        }

        .info-box svg {
            width: 18px;
            height: 18px;
            stroke: #4ade80;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-box.warning svg {
            stroke: #fbbf24;
        }

        .info-box .info-title {
            font-size: 14px;
            font-weight: 600;
            color: #4ade80;
            margin-bottom: 4px;
        }

        .info-box.warning .info-title {
            color: #fbbf24;
        }

        .info-box .info-text {
            font-size: 13px;
            color: #cbd5e1;
            line-height: 1.5;
        }

        .footer-text {
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1>Application submitted successfully</h1>
        <p class="subtitle">
            Thank you! Your application was received on
            {{ $submittedAt ?? now()->format('M j, Y \a\t g:i A') }}.
        </p>

        <div class="info-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <div class="info-title">What happens next?</div>
                <div class="info-text">Our team will review your application and contact you if we need anything further.</div>
            </div>
        </div>

        <div class="info-box warning">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <div>
                <div class="info-title">This link is no longer available</div>
                <div class="info-text">For security, this link can only be used once. It can't be used to submit another application.</div>
            </div>
        </div>

        <p class="footer-text">Need to submit another application? Please contact the person who originally shared this link with you.</p>
    </div>
</body>

</html>