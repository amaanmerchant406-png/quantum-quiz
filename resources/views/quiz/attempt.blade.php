@extends('layouts.app')

@section('title', 'Scorecard')

@section('styles')
<style>
    .results-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        margin-top: 2rem;
    }
    @media (min-width: 850px) {
        .results-grid {
            grid-template-columns: 350px 1fr;
        }
    }
    .accuracy-circle {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 1.5rem auto;
    }
    .accuracy-svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }
    .accuracy-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.75rem;
        font-weight: 800;
        text-align: center;
    }
    .accuracy-pct {
        font-family: 'Orbitron', sans-serif;
    }
    .review-question-card {
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }
    .review-question-card:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .badge-result {
        display: inline-flex;
        padding: 0.15rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        border: 1px solid currentColor;
    }
    .badge-correct { color: var(--success); }
    .badge-incorrect { color: var(--danger); }
</style>
@endsection

@section('content')
<!-- Header Title -->
<div style="margin-bottom: 2.5rem;">
    <h1 class="tech-font glow-cyan" style="font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem;">SCORECARD</h1>
    <p style="color: var(--text-muted); font-size: 1rem;">Mission evaluation complete. Review telemetry below.</p>
</div>

<!-- Level Up Overlay Banner (Conditional) -->
<div id="level-up-banner" class="glass-panel" style="display: none; background: rgba(139, 92, 246, 0.15); border: 2px solid var(--primary); text-align: center; margin-bottom: 2.5rem; animation: pulseGlow 2s infinite alternate;">
    <h2 class="tech-font glow-primary" style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; color: #fff;">LEVEL UP!</h2>
    <p style="font-size: 1.15rem; color: var(--text-main); margin-bottom: 0.5rem;">You have ascended to Level <span id="level-up-val">{{ $attempt->user->level }}</span></p>
    <p style="font-size: 0.95rem; color: var(--text-muted);">Confetti matrix initialized. Keep pushing boundaries.</p>
</div>

<div class="results-grid">
    <!-- Left Sidebar: Analytics Overview -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <div class="glass-panel" style="text-align: center; position: sticky; top: 120px;">
            <!-- Circle Graph -->
            <div class="accuracy-circle">
                <svg class="accuracy-svg" viewBox="0 0 36 36">
                    <!-- background path -->
                    <path class="accuracy-bg"
                        style="fill: none; stroke: rgba(255,255,255,0.05); stroke-width: 3.5;"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <!-- foreground progress path -->
                    @php
                        $percentage = ($attempt->total_questions > 0) ? ($attempt->correct_answers / $attempt->total_questions) * 100 : 0;
                    @endphp
                    <path class="accuracy-fill"
                        style="fill: none; stroke: var(--cyan); stroke-width: 3.5; stroke-dasharray: {{ $percentage }}, 100; stroke-linecap: round; transition: stroke-dasharray 1s ease;"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                </svg>
                <div class="accuracy-text">
                    <span class="accuracy-pct glow-cyan">{{ round($percentage) }}%</span>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-top: 0.25rem;">Accuracy</div>
                </div>
            </div>

            <!-- Stats Table -->
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; text-align: left;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Points Scored</span>
                    <span class="tech-font" style="font-weight: 700; color: var(--warning);">{{ $attempt->score }} pts</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">XP Earned</span>
                    <span class="tech-font" style="font-weight: 700; color: var(--primary);">+{{ $attempt->score }} XP</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Time Taken</span>
                    <span class="tech-font" style="font-weight: 700; color: #fff;">{{ $attempt->time_taken }}s</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">Correct Answers</span>
                    <span class="tech-font" style="font-weight: 700; color: var(--success);">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</span>
                </div>
            </div>

            <!-- Back to Dashboard -->
            <div style="margin-top: 2rem;">
                <a href="{{ route('dashboard') }}" class="btn-neon tech-font" style="width: 100%; justify-content: center;">
                    RETURN TO DECK
                </a>
            </div>
        </div>

    </div>

    <!-- Right Content: Detailed Review -->
    <div class="glass-panel">
        <h3 class="tech-font" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
            TELEMETRY REVIEW
        </h3>

        <div>
            @foreach($attempt->quiz->questions as $index => $q)
                @php
                    // Retrieve user's answer if logged
                    // In submitAttempt we saved userAnswers under the session or request. We can review them.
                    // Wait, let's see how we can fetch the user's selected option.
                    // We don't save the exact selected option in DB (just score/correct counts), BUT we can mock it
                    // or let's check: did we save the user's options?
                    // Ah, the standard QuizAttempt doesn't store option details, only aggregate scores.
                    // But wait, to show a detailed question-by-question review, it would be awesome to know which was correct.
                    // Since it's a review, we can show:
                    // 1. Question statement
                    // 2. Options list, with the Correct Option highlighted in green.
                    // This is perfectly fine and helpful enough for a standard scorecard review! Let's display the options list, highlighting the correct option, and let the user know what was the correct answer.
                @endphp
                <div class="review-question-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; gap: 1rem;">
                        <span style="font-weight: 700; color: var(--text-muted); font-size: 0.95rem;">Q{{ $index + 1 }}</span>
                        <span class="badge-result badge-correct">Value: {{ $q->points }} XP</span>
                    </div>

                    <h4 style="font-size: 1.1rem; font-weight: 500; line-height: 1.4; margin-bottom: 1rem; color: #fff;">
                        {{ $q->question_text }}
                    </h4>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                        @foreach($q->options as $opt)
                            <div style="
                                padding: 0.85rem 1rem; 
                                border-radius: 8px; 
                                font-size: 0.95rem; 
                                display: flex; 
                                align-items: center; 
                                justify-content: space-between;
                                background: {{ $opt->is_correct ? 'rgba(16, 185, 129, 0.1)' : 'rgba(255, 255, 255, 0.01)' }};
                                border: 1px solid {{ $opt->is_correct ? 'var(--success)' : 'var(--glass-border)' }};
                            ">
                                <span style="color: {{ $opt->is_correct ? '#fff' : 'var(--text-muted)' }}; font-weight: {{ $opt->is_correct ? '600' : '400' }};">
                                    {{ $opt->option_text }}
                                </span>
                                @if($opt->is_correct)
                                    <span style="color: var(--success); font-size: 0.75rem; font-weight: 700; font-family: 'Orbitron', sans-serif;">CORRECT TARGET</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Trigger celebration effects if level up flag is found
    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('level_up_occurred') === 'true') {
            // Remove flag
            localStorage.removeItem('level_up_occurred');
            
            // Show level up banner
            const banner = document.getElementById('level-up-banner');
            if (banner) {
                banner.style.display = 'block';
            }
            
            // Shower confetti!
            if (window.triggerLevelUp) {
                setTimeout(() => {
                    window.triggerLevelUp();
                }, 500);
            }
        }
    });
</script>
<style>
    @keyframes pulseGlow {
        0% { box-shadow: 0 0 10px rgba(139, 92, 246, 0.2); }
        100% { box-shadow: 0 0 25px rgba(139, 92, 246, 0.6); }
    }
</style>
@endsection
