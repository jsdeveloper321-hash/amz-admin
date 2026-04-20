<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Privacy Policy — Amazing Pro Drivers" />
    <title>Privacy Policy | Amazing Pro Drivers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #0a0d12;
            --surface: #121820;
            --border: rgba(255, 255, 255, 0.08);
            --text: #e8ecf1;
            --muted: #8b95a5;
            --accent: #e8a838;
            --accent-dim: rgba(232, 168, 56, 0.14);
            --glow: rgba(232, 168, 56, 0.28);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: "DM Sans", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.65;
            min-height: 100vh;
        }

        .noise {
            pointer-events: none;
            position: fixed;
            inset: 0;
            opacity: 0.035;
            z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        .gradient-orb {
            position: fixed;
            width: 75vmin;
            height: 75vmin;
            border-radius: 50%;
            background: radial-gradient(circle, var(--glow) 0%, transparent 68%);
            top: -22vmin;
            right: -18vmin;
            z-index: 0;
            filter: blur(64px);
        }

        header {
            position: relative;
            z-index: 1;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.02em;
            color: var(--text);
            text-decoration: none;
        }

        .logo span { color: var(--accent); }

        nav { display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap; }

        nav a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav a:hover { color: var(--text); }

        main {
            position: relative;
            z-index: 1;
            max-width: 760px;
            margin: 0 auto;
            padding: 2rem 1.5rem 5rem;
        }

        .page-header {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .page-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: var(--accent-dim);
            color: var(--accent);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        h1 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: clamp(2rem, 6vw, 2.75rem);
            font-weight: 400;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .effective-date {
            color: var(--muted);
            font-size: 0.875rem;
        }

        .section {
            margin-bottom: 2.5rem;
        }

        .section h2 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: 1.35rem;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 0.75rem;
        }

        .section p, .section li {
            color: var(--muted);
            font-size: 0.975rem;
            margin-bottom: 0.75rem;
        }

        .section ul {
            padding-left: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .section li { margin-bottom: 0.4rem; }

        .section p:last-child, .section ul:last-child { margin-bottom: 0; }

        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem 1.5rem;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.8rem;
        }

        footer nav { justify-content: center; margin-bottom: 0.5rem; }
        footer nav a { color: var(--muted); font-size: 0.8rem; }
        footer nav a:hover { color: var(--text); }
    </style>
</head>
<body>
<div class="noise" aria-hidden="true"></div>
<div class="gradient-orb" aria-hidden="true"></div>

<header>
    <a href="{{ url('/') }}" class="logo">Amazing<span>Pro</span>Drivers</a>
    <nav>
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/terms-of-service') }}">Terms of Service</a>
    </nav>
</header>

<main>
    <div class="page-header">
        <div class="page-badge">Legal</div>
        <h1>Privacy Policy</h1>
        <p class="effective-date">Effective date: April 20, 2026</p>
    </div>

    <div class="section">
        <h2>1. Who We Are</h2>
        <p>Amazing Pro Drivers ("we", "us", or "our") operates a mobile application and website designed as a community platform for professional truck drivers. This Privacy Policy explains how we collect, use, and protect your personal information when you use our services.</p>
    </div>

    <div class="section">
        <h2>2. Information We Collect</h2>
        <p>We collect information you provide directly to us, including:</p>
        <ul>
            <li>Account information: name, email address, phone number, and CDL details during registration.</li>
            <li>Profile information: trucking experience, home base region, availability status, and any bio you choose to share.</li>
            <li>Location data: approximate location when you voluntarily enable availability mode, so nearby drivers can find shared rest stops and services.</li>
            <li>Communications: messages sent through the app's community features.</li>
            <li>Device information: device type, operating system, and app version for support and analytics.</li>
        </ul>
        <p>We do not collect your precise GPS coordinates without explicit consent, and you can disable location sharing at any time in the app settings.</p>
    </div>

    <div class="section">
        <h2>3. How We Use Your Information</h2>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Create and maintain your account and profile.</li>
            <li>Enable community features such as shared rest stops, driver availability, and announcements.</li>
            <li>Send you important service updates and safety notifications.</li>
            <li>Improve and troubleshoot the app through aggregated, anonymized analytics.</li>
            <li>Comply with legal obligations and enforce our Terms of Service.</li>
        </ul>
        <p>We do not sell your personal information to third parties.</p>
    </div>

    <div class="section">
        <h2>4. Sharing of Information</h2>
        <p>We may share your information only in the following circumstances:</p>
        <ul>
            <li><strong style="color: var(--text);">Within the community:</strong> Profile information you mark as visible will be shown to other verified drivers in the app.</li>
            <li><strong style="color: var(--text);">Service providers:</strong> Trusted vendors who help us operate the platform (hosting, analytics, push notifications) under strict confidentiality agreements.</li>
            <li><strong style="color: var(--text);">Legal requirements:</strong> When required by law, court order, or to protect the safety of our users.</li>
        </ul>
    </div>

    <div class="section">
        <h2>5. Data Retention</h2>
        <p>We retain your account data for as long as your account is active. If you delete your account, we will remove your personal information within 30 days, except where retention is required by law or for legitimate business purposes such as fraud prevention.</p>
    </div>

    <div class="section">
        <h2>6. Security</h2>
        <p>We use industry-standard security measures including encryption in transit (TLS) and at rest. However, no method of transmission over the internet is 100% secure. We encourage you to use a strong, unique password and to report any suspicious activity to us promptly.</p>
    </div>

    <div class="section">
        <h2>7. Your Rights</h2>
        <p>Depending on your location, you may have the right to:</p>
        <ul>
            <li>Access the personal information we hold about you.</li>
            <li>Correct inaccurate information.</li>
            <li>Request deletion of your data.</li>
            <li>Opt out of non-essential communications.</li>
        </ul>
        <p>To exercise any of these rights, contact us at <a href="mailto:privacy@amazingprodrivers.com">privacy@amazingprodrivers.com</a>.</p>
    </div>

    <div class="section">
        <h2>8. Children's Privacy</h2>
        <p>Our service is intended for professional truck drivers aged 18 and older. We do not knowingly collect personal information from anyone under 18. If you believe a minor has provided us with personal information, please contact us and we will delete it promptly.</p>
    </div>

    <div class="section">
        <h2>9. Changes to This Policy</h2>
        <p>We may update this Privacy Policy from time to time. When we do, we will post the revised policy with a new effective date and notify you via the app or email. Your continued use of the service after the effective date constitutes acceptance of the updated policy.</p>
    </div>

    <div class="section">
        <h2>10. Contact Us</h2>
        <p>If you have questions or concerns about this Privacy Policy, please reach out:</p>
        <p>Email: <a href="mailto:privacy@amazingprodrivers.com">privacy@amazingprodrivers.com</a></p>
    </div>
</main>

<footer>
    <nav aria-label="Legal">
        <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
        <span aria-hidden="true"> · </span>
        <a href="{{ url('/terms-of-service') }}">Terms of Service</a>
    </nav>
    <p>© <span id="y"></span> Amazing Pro Drivers. Community for professional truck drivers.</p>
</footer>
<script>document.getElementById("y").textContent = new Date().getFullYear();</script>
</body>
</html>