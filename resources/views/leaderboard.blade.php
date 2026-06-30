@extends('layouts.app')

@section('title', 'Global Standings')

@section('styles')
<style>
    .podium-container {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        gap: 1.5rem;
        margin: 3rem 0;
        flex-wrap: wrap;
    }
    .podium-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2rem 1.5rem;
        text-align: center;
        width: 100%;
        max-width: 220px;
        position: relative;
        transition: all 0.3s ease;
    }
    .podium-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }
    .podium-1 {
        height: 280px;
        border-color: #ffd700;
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15);
        order: 2;
    }
    .podium-2 {
        height: 240px;
        border-color: #c0c0c0;
        box-shadow: 0 10px 25px rgba(192, 192, 192, 0.1);
        order: 1;
    }
    .podium-3 {
        height: 210px;
        border-color: #cd7f32;
        box-shadow: 0 10px 20px rgba(205, 127, 50, 0.08);
        order: 3;
    }
    .podium-rank {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        margin: -3.5rem auto 1rem auto;
        border: 2px solid currentColor;
    }
    .podium-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--cyan) 100%);
        margin: 0 auto 1rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: #fff;
    }
</style>
@endsection

@section('content')
<!-- Header Title -->
<div style="margin-bottom: 2rem;">
    <h1 class="tech-font glow-cyan" style="font-size: 2.5rem; font-weight: 900; margin-bottom: 0.5rem;">LEADERBOARD</h1>
    <p style="color: var(--text-muted); font-size: 1.1rem;">Behold the highest cognitive scoring entities in the sector.</p>
</div>

<!-- Podium Block -->
@php
    $top3 = $leaderboard->take(3);
    $others = $leaderboard->slice(3);
@endphp

@if($leaderboard->isNotEmpty())
<div class="podium-container">
    <!-- First Place -->
    @if($top3->has(0))
        <div class="podium-card podium-1">
            <div class="podium-rank rank-1" style="background: #ffd700; color: #000;">1</div>
            <div class="podium-avatar" style="box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);">{{ substr($top3[0]->name, 0, 1) }}</div>
            <h3 style="font-weight: 700; font-size: 1.15rem; color: #fff; margin-bottom: 0.25rem;">{{ $top3[0]->name }}</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Level {{ $top3[0]->level }}</span>
            <div class="tech-font" style="font-size: 1.2rem; font-weight: 800; color: var(--warning); margin-top: 1rem;">
                {{ $top3[0]->xp }} XP
            </div>
        </div>
    @endif

    <!-- Second Place -->
    @if($top3->has(1))
        <div class="podium-card podium-2">
            <div class="podium-rank rank-2" style="background: #c0c0c0; color: #000;">2</div>
            <div class="podium-avatar">{{ substr($top3[1]->name, 0, 1) }}</div>
            <h3 style="font-weight: 700; font-size: 1.1rem; color: #fff; margin-bottom: 0.25rem;">{{ $top3[1]->name }}</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Level {{ $top3[1]->level }}</span>
            <div class="tech-font" style="font-size: 1.1rem; font-weight: 800; color: var(--warning); margin-top: 0.75rem;">
                {{ $top3[1]->xp }} XP
            </div>
        </div>
    @endif

    <!-- Third Place -->
    @if($top3->has(2))
        <div class="podium-card podium-3">
            <div class="podium-rank rank-3" style="background: #cd7f32; color: #000;">3</div>
            <div class="podium-avatar">{{ substr($top3[2]->name, 0, 1) }}</div>
            <h3 style="font-weight: 700; font-size: 1.05rem; color: #fff; margin-bottom: 0.25rem;">{{ $top3[2]->name }}</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Level {{ $top3[2]->level }}</span>
            <div class="tech-font" style="font-size: 1.05rem; font-weight: 800; color: var(--warning); margin-top: 0.75rem;">
                {{ $top3[2]->xp }} XP
            </div>
        </div>
    @endif
</div>

<!-- Remaining Leaderboard Table List -->
<div class="glass-panel" style="margin-top: 3rem;">
    <h3 class="tech-font" style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.5rem;">SECTOR RANKINGS</h3>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                    <th style="padding: 1rem 0.75rem;">Rank</th>
                    <th style="padding: 1rem 0.75rem;">Explorer Name</th>
                    <th style="padding: 1rem 0.75rem;">Sector Role</th>
                    <th style="padding: 1rem 0.75rem; text-align: center;">Level</th>
                    <th style="padding: 1rem 0.75rem; text-align: right;">Total Experience</th>
                </tr>
            </thead>
            <tbody>
                @foreach($others as $index => $runner)
                    <tr style="border-bottom: 1px solid var(--glass-border); font-size: 0.95rem;" class="leaderboard-row-anim">
                        <td style="padding: 1rem 0.75rem;" class="tech-font">#{{ $index + 4 }}</td>
                        <td style="padding: 1rem 0.75rem; font-weight: 600; color: #fff;">{{ $runner->name }}</td>
                        <td style="padding: 1rem 0.75rem; color: var(--text-muted); text-transform: capitalize;">{{ $runner->role }}</td>
                        <td style="padding: 1rem 0.75rem; text-align: center;" class="tech-font">{{ $runner->level }}</td>
                        <td style="padding: 1rem 0.75rem; text-align: right; color: var(--warning); font-weight: 700;" class="tech-font">{{ $runner->xp }} XP</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
    <div class="glass-panel" style="text-align: center; padding: 4rem 2rem;">
        <p style="color: var(--text-muted); font-size: 1rem;">No rank listings processed yet.</p>
    </div>
@endif
@endsection
