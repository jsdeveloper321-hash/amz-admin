<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Terms of Service — Amazing Pro Drivers" />
    <title>Terms of Service | Amazing Pro Drivers</title>
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
        <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
    </nav>
</header>

<main>
    <div class="page-header">
        <div class="page-badge">Legal</div>
        <h1>Terms of Service</h1>
        <p class="effective-date">Effective date: April 20, 2026</p>
    </div>

    <div class="section">
        <h2>1. Acceptance of Terms</h2>
        <p>By downloading, installing, or using the Amazing Pro Drivers application or website (the "Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, do not use the Service.</p>
    </div>

    <div class="section">
        <h2>2. Eligibility</h2>
        <p>You must be at least 18 years old and hold a valid Commercial Driver's License (CDL) or be otherwise actively employed in the professional trucking industry to create an account. By using the Service, you represent and warrant that you meet these requirements.</p>
    </div>

    <div class="section">
        <h2>3. Your Account</h2>
        <p>You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. You agree to:</p>
        <ul>
            <li>Provide accurate, current, and complete information during registration.</li>
            <li>Promptly update your information if it changes.</li>
            <li>Notify us immediately of any unauthorized use of your account.</li>
        </ul>
        <p>We reserve the right to suspend or terminate accounts that violate these Terms or that we believe pose a risk to the community.</p>
    </div>

    <div class="section">
        <h2>4. Community Standards</h2>
        <p>Amazing Pro Drivers is built on mutual respect. When using the Service, you agree not to:</p>
        <ul>
            <li>Harass, threaten, or demean other users.</li>
            <li>Share false, misleading, or defamatory information.</li>
            <li>Post content that is sexually explicit, hateful, or discriminatory.</li>
            <li>Solicit money, goods, or services from other drivers in an unauthorized manner.</li>
            <li>Impersonate another person or entity.</li>
            <li>Attempt to gain unauthorized access to other accounts or our systems.</li>
        </ul>
        <p>Violations may result in immediate account termination without notice.</p>
    </div>

    <div class="section">
        <h2>5. Availability and Location Features</h2>
        <p>The app allows you to voluntarily indicate your availability to meet fellow drivers during legal off-duty time. By enabling availability mode, you acknowledge that:</p>
        <ul>
            <li>Your approximate location and profile will be visible to other verified users in the app.</li>
            <li>You have full control to disable availability at any time.</li>
            <li>We are not responsible for in-person interactions between users. Exercise personal judgment and follow all applicable laws and regulations.</li>
        </ul>
    </div>

    <div class="section">
        <h2>6. User Content</h2>
        <p>You retain ownership of content you submit to the Service (posts, profile info, messages). By submitting content, you grant Amazing Pro Drivers a non-exclusive, royalty-free license to use, display, and distribute that content solely for the purpose of operating and improving the Service. We do not claim ownership of your content.</p>
        <p>You are solely responsible for the content you share and warrant that it does not infringe any third-party rights.</p>
    </div>

    <div class="section">
        <h2>7. Intellectual Property</h2>
        <p>The Service, including its design, logo, code, and content created by us, is owned by Amazing Pro Drivers and protected by applicable intellectual property laws. You may not copy, modify, distribute, or create derivative works without our prior written consent.</p>
    </div>

    <div class="section">
        <h2>8. Disclaimers</h2>
        <p>The Service is provided "as is" and "as available" without warranties of any kind, express or implied. We do not warrant that the Service will be uninterrupted, error-free, or free of viruses or other harmful components. Information shared by community members (rest stop tips, route advice, hotel leads) is user-generated and we make no guarantees as to its accuracy or safety.</p>
    </div>

    <div class="section">
        <h2>9. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, Amazing Pro Drivers shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of or inability to use the Service, even if we have been advised of the possibility of such damages. Our total liability to you for any claims arising from the Service shall not exceed the amount you paid us in the twelve months preceding the claim.</p>
    </div>

    <div class="section">
        <h2>10. Termination</h2>
        <p>You may delete your account at any time through the app settings. We may suspend or terminate your access to the Service at any time, with or without cause, and with or without notice. Upon termination, your right to use the Service ceases immediately.</p>
    </div>

    <div class="section">
        <h2>11. Changes to Terms</h2>
        <p>We may modify these Terms from time to time. We will notify you of material changes via the app or email. Your continued use of the Service after the effective date of the revised Terms constitutes your acceptance of the changes.</p>
    </div>

    <div class="section">
        <h2>12. Governing Law</h2>
        <p>These Terms shall be governed by and construed in accordance with the laws of the United States. Any disputes shall be resolved through binding arbitration, except where prohibited by law.</p>
    </div>

    <div class="section">
        <h2>13. Contact Us</h2>
        <p>Questions about these Terms? Reach out to us:</p>
        <p>Email: <a href="mailto:legal@amazingprodrivers.com">legal@amazingprodrivers.com</a></p>
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