@extends('layouts.app')

@section('title', 'Control Deck')

@section('content')
<!-- Top Headline Header -->
<div style="margin-bottom: 3rem;">
    <h1 class="tech-font glow-cyan" style="font-size: 2.5rem; font-weight: 900; margin-bottom: 0.5rem;">DASHBOARD</h1>
    <p style="color: var(--text-muted); font-size: 1.1rem;">Select a deployment and initialize cognitive testing.</p>
</div>

<!-- Stats Row -->
<div class="stats-grid">
    <div class="glass-panel stat-card">
        <span class="stat-label">Cognitive Rank</span>
        <span class="stat-value glow-cyan">#{{ $userRank }}</span>
    </div>
    <div class="glass-panel stat-card stat-purple">
        <span class="stat-label">Experience Points</span>
        <span class="stat-value glow-primary">{{ Auth::user()->xp }} XP</span>
    </div>
    <div class="glass-panel stat-card">
        <span class="stat-label">Deployments Checked</span>
        <span class="stat-value">{{ $userAttemptsCount }}</span>
    </div>
    <div class="glass-panel stat-card stat-amber">
        <span class="stat-label">Average Performance</span>
        <span class="stat-value">{{ number_format($userAverageScore, 1) }} pts</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 2.5rem; align-items: start;" id="main-deck">
    <!-- Main Quiz Deck -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Search & Category Filters -->
        <div class="glass-panel" style="padding: 1.25rem 1.5rem; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: space-between;">
            
            <!-- Category Tags list -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                <a href="{{ route('dashboard') }}" 
                   class="btn-glass {{ !request()->filled('category') ? 'btn-neon' : '' }}" 
                   style="padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px;">
                    All Categories
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('dashboard', ['category' => $category, 'search' => request('search')]) }}" 
                       class="btn-glass {{ request('category') === $category ? 'btn-neon' : '' }}" 
                       style="padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px;">
                        {{ $category }}
                    </a>
                @endforeach
            </div>

            <!-- Search box -->
            <form action="{{ route('dashboard') }}" method="GET" style="display: flex; gap: 0.5rem; margin: 0; width: 100%; max-width: 320px;">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="search" class="form-input" placeholder="Search mission..." value="{{ request('search') }}" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                <button type="submit" class="btn-glass" style="padding: 0.5rem 1rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </form>

        </div>

        <!-- Quizzes Grid -->
        <div class="quiz-grid">
            @forelse($quizzes as $quiz)
                <div class="glass-panel glass-panel-hover quiz-card 3d-tilt" data-difficulty="{{ $quiz->difficulty }}">
                    <div>
                        <div class="quiz-category tech-font">{{ $quiz->category }}</div>
                        <span class="quiz-difficulty diff-{{ $quiz->difficulty }}">{{ $quiz->difficulty }}</span>
                        
                        <h2 class="quiz-title">{{ $quiz->title }}</h2>
                        <p class="quiz-desc">{{ Str::limit($quiz->description, 120) }}</p>
                    </div>

                    <div class="quiz-footer">
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <span class="quiz-meta">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $quiz->questions->count() }} Questions
                            </span>
                            <span class="quiz-meta">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $quiz->time_limit }}s Limit/Q
                            </span>
                        </div>
                        
                        <a href="{{ route('quiz.play', $quiz->id) }}" class="btn-neon tech-font" style="padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 8px;">
                            INITIALIZE
                        </a>
                    </div>
                </div>
            @empty
                <div class="glass-panel" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                    <svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">No deployments found</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">Adjust filters or head to the Creator Studio to launch a new quiz.</p>
                    <a href="{{ route('creator.create') }}" class="btn-neon tech-font">Launch Quiz Creator</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar: Top Rankers -->
    <div class="glass-panel" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 class="tech-font glow-primary" style="font-size: 1.15rem; font-weight: 800;">TOP NETWORK RUNNERS</h3>
            <a href="{{ route('leaderboard') }}" style="color: var(--cyan); text-decoration: none; font-size: 0.8rem; font-weight: 600;">View All</a>
        </div>
        
        <ul class="leaderboard-list">
            @foreach($leaderboard as $index => $leadUser)
                <li class="leaderboard-item">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span class="leaderboard-rank-badge rank-{{ $index + 1 }} tech-font">
                            {{ $index + 1 }}
                        </span>
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-weight: 600; font-size: 0.95rem; color: #fff;">{{ $leadUser->name }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Level {{ $leadUser->level }}</span>
                        </div>
                    </div>
                    <span class="tech-font" style="font-weight: 800; font-size: 0.95rem; color: var(--warning);">
                        {{ $leadUser->xp }} XP
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 3D Card Tilt Effect Script
    const cards = document.querySelectorAll('.3d-tilt');
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left; // x position inside element
            const y = e.clientY - rect.top;  // y position inside element
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * 10; // Max 10 deg rotation
            const rotateY = ((centerX - x) / centerX) * 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0)';
        });
    });
</script>
@endsection
