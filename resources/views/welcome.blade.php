<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Amazing Pro Drivers — a community for professional truck drivers: shared resets, trusted stops, and respect for everyone who keeps freight moving." />
    <title>Amazing Pro Drivers | Community for Professional Truck Drivers</title>
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

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

        .logo span {
            color: var(--accent);
        }

        nav {
            display: flex;
            gap: 1.25rem;
            align-items: center;
            flex-wrap: wrap;
        }

        nav a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav a:hover {
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f0b84a 0%, #c78a1a 100%);
            color: #1a1204;
            box-shadow: 0 4px 24px var(--accent-dim);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(232, 168, 56, 0.3);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--surface);
            border-color: rgba(255, 255, 255, 0.14);
        }

        main {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4.5rem;
        }

        .hero {
            text-align: center;
            padding: 1.5rem 0 3rem;
        }

        .hero-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: var(--accent-dim);
            color: var(--accent);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }

        .hero h1 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: clamp(2.25rem, 7vw, 3.5rem);
            font-weight: 400;
            line-height: 1.12;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--accent);
        }

        .hero-lead {
            color: var(--muted);
            font-size: 1.05rem;
            max-width: 34rem;
            margin: 0 auto 1.75rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        .letter {
            margin-bottom: 3.5rem;
            padding: 2.25rem 2rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1rem;
            border-left: 4px solid var(--accent);
        }

        .letter h2 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: 1.65rem;
            font-weight: 400;
            margin-bottom: 1.25rem;
            color: var(--text);
        }

        .letter p {
            color: var(--muted);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .letter p:last-child {
            margin-bottom: 0;
        }

        .letter .signoff {
            margin-top: 1.5rem;
            color: var(--text);
            font-weight: 500;
        }

        section {
            margin-bottom: 3.25rem;
        }

        section > h2 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 400;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .section-lead {
            text-align: center;
            color: var(--muted);
            max-width: 38rem;
            margin: 0 auto 2rem;
            font-size: 0.98rem;
        }

        .hos {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            margin-bottom: 2rem;
        }

        .hos-card {
            padding: 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.875rem;
            text-align: center;
        }

        .hos-card strong {
            display: block;
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--accent);
            font-variant-numeric: tabular-nums;
            margin-bottom: 0.35rem;
        }

        .hos-card span {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.45;
        }

        .hos-note {
            font-size: 0.8rem;
            color: var(--muted);
            text-align: center;
            max-width: 36rem;
            margin: 0 auto;
            font-style: italic;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.1rem;
        }

        .card {
            padding: 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.875rem;
            transition: border-color 0.2s, transform 0.2s;
        }

        .card:hover {
            border-color: rgba(232, 168, 56, 0.22);
            transform: translateY(-2px);
        }

        .card-icon {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 0.45rem;
            background: var(--accent-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 0.85rem;
        }

        .card h3 {
            font-size: 1.02rem;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .card p {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .together {
            padding: 2.25rem 1.75rem;
            background: linear-gradient(180deg, var(--surface) 0%, rgba(18, 24, 32, 0.6) 100%);
            border: 1px solid var(--border);
            border-radius: 1rem;
            text-align: center;
        }

        .together h3 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: 1.35rem;
            font-weight: 400;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .together p {
            color: var(--muted);
            max-width: 32rem;
            margin: 0 auto 1rem;
            font-size: 0.98rem;
        }

        .together ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
            justify-content: center;
            margin-top: 1.25rem;
        }

        .together li {
            padding: 0.4rem 0.85rem;
            background: var(--accent-dim);
            color: var(--accent);
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .cta {
            text-align: center;
            padding: 2.75rem 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1rem;
        }

        .cta h2 {
            font-family: "Instrument Serif", Georgia, serif;
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 0.65rem;
        }

        .cta p {
            color: var(--muted);
            margin-bottom: 1.35rem;
            max-width: 28rem;
            margin-left: auto;
            margin-right: auto;
            font-size: 0.95rem;
        }

        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem 1.5rem;
            color: var(--muted);
            font-size: 0.82rem;
            border-top: 1px solid var(--border);
        }

        footer nav {
            justify-content: center;
            margin-bottom: 1rem;
        }

        footer a {
            color: var(--muted);
        }

        footer a:hover {
            color: var(--accent);
        }

        @media (max-width: 640px) {
            nav a[href="#letter"],
            nav a[href="#app"] {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="noise" aria-hidden="true"></div>
<div class="gradient-orb" aria-hidden="true"></div>

<header>
    <a href="#" class="logo">Amazing <span>Pro</span> Drivers</a>
    <nav aria-label="Primary">
        <a href="#letter">Letter</a>
        <a href="#community">Community</a>
        <a href="#app">The app</a>
        <a href="{{ url('privacy-policy') }}">Privacy</a>
        <a href="{{ url('terms-of-service') }}">Terms</a>
        <a href="#download" class="btn btn-primary">Get the app</a>
    </nav>
</header>

<main>
    <section class="hero">
        <p class="hero-badge">Built by drivers, for drivers</p>
        <h1>To every professional behind the wheel—<em>thank you</em></h1>
        <p class="hero-lead">
            Amazing Pro Drivers is a community that honors long-haul work, looks out for one another on the road, and makes the miles a little more human.
        </p>
        <div class="hero-actions">
            <a href="#download" class="btn btn-primary">Join the community</a>
            <a href="#letter" class="btn btn-ghost">Read our letter</a>
        </div>
    </section>

    <article id="letter" class="letter" aria-labelledby="letter-heading">
        <h2 id="letter-heading">An appreciation to every truck driver</h2>
        <p>
            You work long hours, often far from home, while the people you love carry on daily life without you in the room. You miss birthdays, school events, and quiet evenings—because someone has to move the freight that keeps hospitals supplied, store shelves filled, and construction sites building our future.
        </p>
        <p>
            Rain, ice, traffic, tight docks, and endless highway do not pause for convenience. You shoulder responsibility for enormous machines and for cargo that strangers depend on, usually without fanfare or thanks. That effort deserves more than a passing mention—it deserves real respect.
        </p>
        <p>
            At Amazing Pro Drivers, we see you as individuals: every single driver who holds a CDL and does this job with skill and grit. We appreciate <em>you</em>—not as an abstract “industry,” but as people who sacrifice comfort and closeness so essential products reach every corner of our lives.
        </p>
        <p class="signoff">
            With gratitude,<br />
            The Amazing Pro Drivers community
        </p>
    </article>

    <section id="community" aria-labelledby="community-heading">
        <h2 id="community-heading">The rhythm we all know</h2>
        <p class="section-lead">
            Under federal hours-of-service rules, many over-the-road drivers operate within a <strong style="color: var(--text); font-weight: 600;">70-hour</strong> limit in eight days (or 60 hours in seven, depending on carrier). A <strong style="color: var(--text); font-weight: 600;">34-hour</strong> off-duty restart can reset that weekly clock when taken according to current regulations. We live inside those numbers—and we still choose to look out for each other.
        </p>
        <div class="hos" role="group" aria-label="Hours of service highlights">
            <div class="hos-card">
                <strong>70</strong>
                <span>On-duty hours in 8 days (typical 7/8-day limit for many property carriers)</span>
            </div>
            <div class="hos-card">
                <strong>34</strong>
                <span>Consecutive hours off duty for a qualifying restart (when rules allow)</span>
            </div>
            <div class="hos-card">
                <strong>1</strong>
                <span>Community: drivers helping drivers, mile after mile</span>
            </div>
        </div>
        <p class="hos-note">
            Regulations can change; always follow FMCSA rules, your company policy, and your electronic logging device. This site summarizes common concepts—not legal advice.
        </p>
    </section>

    <section aria-labelledby="together-heading">
        <h2 id="together-heading">Together on reset</h2>
        <p class="section-lead">
            When you are on a 34-hour reset—or any legal break—and another member is in the same area, our community is about quality time: real conversation, shared meals, and getting outdoors when you want company. You choose whether to be available; you choose how you connect.
        </p>
        <div class="together">
            <h3>Same area, same downtime</h3>
            <p>
                Hiking a trail near the truck stop, camping for a night when schedules align, casting a line at a nearby lake—small moments that remind us we are more than a unit number on a dispatch board. If you are open to it, the app helps you signal availability so drivers nearby can reach out respectfully.
            </p>
            <ul>
                <li>Hiking</li>
                <li>Camping</li>
                <li>Fishing</li>
                <li>Shared meals</li>
                <li>Honest friendship</li>
            </ul>
        </div>
    </section>

    <section id="app" aria-labelledby="app-heading">
        <h2 id="app-heading">What you will find in our app</h2>
        <p class="section-lead">
            Practical help for life on the road—crowdsourced and refined by drivers who have been there.
        </p>
        <div class="cards">
            <article class="card">
                <div class="card-icon" aria-hidden="true">🍽</div>
                <h3>Good food, real recommendations</h3>
                <p>Places worth the walk from the lot—where the meal is hot, the portion fair, and drivers are treated like regulars.</p>
            </article>
            <article class="card">
                <div class="card-icon" aria-hidden="true">🅿</div>
                <h3>Truck &amp; trailer parking</h3>
                <p>Spots that respect length, swing-out, and overnight needs—so you are not guessing in the dark.</p>
            </article>
            <article class="card">
                <div class="card-icon" aria-hidden="true">🔧</div>
                <h3>Trusted truck shops</h3>
                <p>Shops that understand Class 8 work—so you can get back rolling with fewer surprises.</p>
            </article>
            <article class="card">
                <div class="card-icon" aria-hidden="true">🏨</div>
                <h3>Hotels when you are broken down</h3>
                <p>Leads on affordable rooms when the truck is in the bay and you need a safe place to sleep—not a luxury suite.</p>
            </article>
            <article class="card">
                <div class="card-icon" aria-hidden="true">📍</div>
                <h3>Availability, on your terms</h3>
                <p>Let others know if you are open to meet during legal off-duty time. Privacy controls and our policies are there to protect you—read them before you share.</p>
            </article>
            <article class="card">
                <div class="card-icon" aria-hidden="true">🤝</div>
                <h3>Help that goes both ways</h3>
                <p>Advice from the road, a second pair of eyes on a route, or a calm voice when the day has been too long—we rise together.</p>
            </article>
        </div>
    </section>

    <section id="download" class="cta" aria-labelledby="download-heading">
        <h2 id="download-heading">Download the app</h2>
        <p>
            Every driver is welcome. Install the Amazing Pro Drivers app, complete onboarding, and accept our Privacy Policy and Terms of Service. Then set your availability when you want company—never when you do not.
        </p>
        <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 1.25rem;">
            App store links go here when your build is live. For now, contact your community organizer or email <a href="mailto:community@amazingprodrivers.example" style="color: var(--accent);">community@amazingprodrivers.example</a> (replace with your real address).
        </p>
        <a href="{{ route('privacy-policy') }}" class="btn btn-ghost" style="margin-right: 0.5rem;">Privacy Policy</a>
        <a href="{{ route('terms-of-service') }}" class="btn btn-ghost">Terms of Service</a>
    </section>
</main>

<footer>
    <nav aria-label="Legal">
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
        <span aria-hidden="true"> · </span>
        <a href="{{ route('terms-of-service') }}">Terms of Service</a>
    </nav>
    <p>© <span id="y"></span> Amazing Pro Drivers. Community for professional truck drivers.</p>
</footer>
<script>
    document.getElementById("y").textContent = new Date().getFullYear();
</script>
</body>
</html>
