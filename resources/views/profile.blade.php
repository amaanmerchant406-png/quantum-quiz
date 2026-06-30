@extends('layouts.app')

@section('title', 'Explorer Profile')

@section('styles')
<style>
    .level-progress-container {
        width: 100%;
        height: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 99px;
        overflow: hidden;
        margin-top: 1rem;
        position: relative;
    }
    .level-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--cyan), var(--primary));
        box-shadow: 0 0 10px var(--cyan-glow);
        border-radius: 99px;
        transition: width 0.8s ease-out;
    }
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        align-items: start;
        margin-top: 2rem;
    }
    @media (min-width: 900px) {
        .profile-grid {
            grid-template-columns: 320px 1fr;
        }
    }
</style>
@endsection

@section('content')
<!-- Title Header -->
<div style="margin-bottom: 2rem;">
    <h1 class="tech-font glow-cyan" style="font-size: 2.5rem; font-weight: 900; margin-bottom: 0.5rem;">EXPLORER PROFILE</h1>
    <p style="color: var(--text-muted); font-size: 1.1rem;">Operational stats log and cognitive timeline.</p>
</div>

<div class="profile-grid">
    <!-- Left Column: User Identity Block -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <div class="glass-panel" style="text-align: center; box-shadow: 0 0 30px rgba(139, 92, 246, 0.1);">
            <div style="
                width: 90px;
                height: 90px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary) 0%, var(--magenta) 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem auto;
                font-size: 2.25rem;
                font-weight: 800;
                color: #fff;
                box-shadow: 0 0 20px rgba(236, 72, 153, 0.4);
            ">
                {{ substr($user->name, 0, 1) }}
            </div>

            <h2 class="tech-font" style="font-size: 1.35rem; font-weight: 800; color: #fff; margin-bottom: 0.25rem;">{{ $user->name }}</h2>
            <span style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">{{ $user->role }}</span>

            <!-- Level progress meter -->
            @php
                $currentXp = $user->xp;
                $level = $user->level;
                // Target XP to next level
                $targetXp = $level * 400;
                // Calculate percentage
                $lvlProgress = ($targetXp > 0) ? ($currentXp / $targetXp) * 100 : 0;
            @endphp
            <div style="margin-top: 2rem; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; font-size: 0.85rem;">
                    <span style="color: var(--text-muted);">Level progress</span>
                    <span class="tech-font" style="font-weight: 700; color: var(--cyan);">{{ $currentXp }} / {{ $targetXp }} XP</span>
                </div>
                <div class="level-progress-container">
                    <div class="level-progress-bar" style="width: {{ min(100, $lvlProgress) }}%;"></div>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem; border-top: 1px solid var(--glass-border); margin-top: 2rem; padding-top: 1.5rem; text-align: left; font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Deployments completed</span>
                    <span style="font-weight: 600; color: #fff;">{{ $totalQuizzes }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Avg. Accuracy</span>
                    <span style="font-weight: 600; color: var(--success);">{{ number_format($avgAccuracy, 1) }}%</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Total XP accumulated</span>
                    <span style="font-weight: 600; color: var(--warning);">{{ $totalXp }} XP</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Historical Attempts timeline -->
    <div class="glass-panel">
        <h3 class="tech-font" style="font-size: 1.2rem; font-weight: 800; margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem;">
            TIMELINE LOGS
        </h3>

        @if($attempts->isEmpty())
            <div style="text-align: center; padding: 4rem 2rem;">
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">No historical attempts logged.</p>
                <a href="{{ route('dashboard') }}" class="btn-neon tech-font">Take First Quiz</a>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--glass-border); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                            <th style="padding: 1rem 0.5rem;">Quiz title</th>
                            <th style="padding: 1rem 0.5rem; text-align: center;">Score</th>
                            <th style="padding: 1rem 0.5rem; text-align: center;">Accuracy</th>
                            <th style="padding: 1rem 0.5rem; text-align: center;">Duration</th>
                            <th style="padding: 1rem 0.5rem; text-align: right;">Completed date</th>
                            <th style="padding: 1rem 0.5rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr style="border-bottom: 1px solid var(--glass-border); font-size: 0.9rem;">
                                <td style="padding: 1.25rem 0.5rem; font-weight: 600; color: #fff;">{{ $attempt->quiz->title }}</td>
                                <td style="padding: 1.25rem 0.5rem; text-align: center; color: var(--warning); font-weight: 700;" class="tech-font">{{ $attempt->score }}</td>
                                <td style="padding: 1.25rem 0.5rem; text-align: center; color: var(--success);" class="tech-font">
                                    {{ round(($attempt->correct_answers / $attempt->total_questions) * 100) }}%
                                </td>
                                <td style="padding: 1.25rem 0.5rem; text-align: center;" class="tech-font">{{ $attempt->time_taken }}s</td>
                                <td style="padding: 1.25rem 0.5rem; text-align: right; color: var(--text-muted);">{{ $attempt->completed_at->format('M d, Y H:i') }}</td>
                                <td style="padding: 1.25rem 0.5rem; text-align: right;">
                                    <a href="{{ route('quiz.attempt', $attempt->id) }}" class="btn-glass" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-radius: 6px;">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
