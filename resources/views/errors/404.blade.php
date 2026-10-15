<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Not Found</title>
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
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 12px;
            padding: 32px;
            text-align: center;
        }
        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-circle svg { width: 26px; height: 26px; stroke: #0b1220; }
        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .subtitle {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 24px;
        }
        .footer-text { font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
        <h1>Link not found</h1>
        <p class="subtitle">
            This link doesn't exist or may have been removed.
        </p>
        <p class="footer-text">Need a new link? Please contact the person who originally shared this with you.</p>
    </div>
</body>
</html>