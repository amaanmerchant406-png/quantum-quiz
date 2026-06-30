<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'QuantumQuiz') | Sleek 3D Gaming Portal</title>
    <meta name="description" content="QuantumQuiz - A high-fidelity, sleek 3D dynamic cognitive assessment and gaming platform. Take quizzes, track XP, rank up, and import questions via Excel.">
    @vite(['resources/css/app.css'])
    @yield('styles')
</head>
<body>
    <!-- Glow Blobs for Futuristic Ambient Light -->
    <div class="glow-blob glow-blob-primary"></div>
    <div class="glow-blob glow-blob-cyan"></div>

    <!-- Navigation Header -->
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="nav-logo tech-font">
            <span>QUANTUM</span>QUIZ
        </a>

        @auth
        <ul class="nav-links">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Portal
                </a>
            </li>
            <li>
                <a href="{{ route('leaderboard') }}" class="nav-link {{ Route::is('leaderboard') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Leaderboard
                </a>
            </li>
            <li>
                <a href="{{ route('creator.index') }}" class="nav-link {{ Route::is('creator.*') ? 'active' : '' }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    Creator Studio
                </a>
            </li>
        </ul>

        <div class="nav-user">
            <a href="{{ route('profile') }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                <span class="tech-font" style="font-weight: 700; color: #fff; font-size: 0.95rem;">{{ Auth::user()->name }}</span>
                <span class="user-badge tech-font">LVL {{ Auth::user()->level }}</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-glass" style="padding: 0.5rem 0.8rem; font-size: 0.85rem; border-radius: 8px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
        @else
        <div class="nav-user">
            <a href="{{ route('login') }}" class="nav-link">Sign In</a>
            <a href="{{ route('register') }}" class="btn-neon tech-font" style="padding: 0.5rem 1.2rem; font-size: 0.85rem; border-radius: 8px;">Join Mission</a>
        </div>
        @endauth
    </nav>

    <!-- Success & Error Alert Containers -->
    <div class="alert-container">
        @if(session('success'))
            <div class="custom-alert alert-success">
                <svg width="20" height="20" fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="custom-alert alert-error">
                <svg width="20" height="20" fill="none" stroke="var(--danger)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <main style="padding: 2.5rem 5%; max-width: 1400px; margin: 0 auto; width: 100%;">
        @yield('content')
    </main>

    <!-- Canvas for Particle Confetti Celebration -->
    <canvas id="confetti-canvas"></canvas>

    <script>
        // Auto-dismiss alerts after 4 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.custom-alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);

        // Core dynamic animations trigger
        window.triggerLevelUp = function() {
            const canvas = document.getElementById('confetti-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            let particles = [];
            const colors = ['#8b5cf6', '#06b6d4', '#ec4899', '#10b981', '#f59e0b'];

            for (let i = 0; i < 150; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    r: Math.random() * 6 + 4,
                    d: Math.random() * canvas.height,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    tilt: Math.random() * 10 - 5,
                    tiltAngleIncremental: Math.random() * 0.07 + 0.02,
                    tiltAngle: 0
                });
            }

            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach((p, idx) => {
                    p.tiltAngle += p.tiltAngleIncremental;
                    p.y += (Math.cos(p.d) + 3 + p.r / 2) / 2;
                    p.x += Math.sin(p.tiltAngle);
                    p.tilt = Math.sin(p.tiltAngle - idx / 3) * 15;

                    ctx.beginPath();
                    ctx.lineWidth = p.r;
                    ctx.strokeStyle = p.color;
                    ctx.moveTo(p.x + p.tilt + p.r / 2, p.y);
                    ctx.lineTo(p.x + p.tilt, p.y + p.tilt + p.r / 2);
                    ctx.stroke();
                });

                update();
            }

            let animationId;
            function update() {
                let remaining = particles.filter(p => p.y < canvas.height);
                if (remaining.length > 0) {
                    particles = remaining;
                    animationId = requestAnimationFrame(draw);
                } else {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    cancelAnimationFrame(animationId);
                }
            }
            draw();
        };
    </script>
    @yield('scripts')
</body>
</html>
