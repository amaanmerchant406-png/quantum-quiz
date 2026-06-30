@extends('layouts.app')

@section('title', 'Creator Studio')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 1.5rem;">
    <div>
        <h1 class="tech-font glow-primary" style="font-size: 2.5rem; font-weight: 900; margin-bottom: 0.5rem;">CREATOR STUDIO</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Upload spreadsheets, compile questions, and monitor network statistics.</p>
    </div>
    
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('creator.template.download') }}" class="btn-glass">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--cyan);"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Excel Template
        </a>
        <a href="{{ route('creator.create') }}" class="btn-neon tech-font">
            Launch New Quiz
        </a>
    </div>
</div>

<div class="glass-panel">
    <h2 class="tech-font" style="font-size: 1.3rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
        MY LAUNCHED QUIZZES
    </h2>

    @if($quizzes->isEmpty())
        <div style="text-align: center; padding: 4rem 2rem;">
            <svg width="48" height="48" fill="none" stroke="var(--text-muted)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">No quizzes created yet</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">You haven't uploaded or built any quizzes. Launch the designer to get started.</p>
            <a href="{{ route('creator.create') }}" class="btn-neon tech-font">Build First Quiz</a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($quizzes as $quiz)
                <div class="glass-panel-hover" style="
                    background: rgba(255, 255, 255, 0.01);
                    border: 1px solid var(--glass-border);
                    border-radius: 12px;
                    padding: 1.25rem 1.5rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 1.5rem;
                ">
                    <div>
                        <span class="tech-font" style="font-size: 0.7rem; color: var(--cyan); text-transform: uppercase; font-weight: 700;">{{ $quiz->category }}</span>
                        <h3 style="font-size: 1.2rem; font-weight: 600; margin-top: 0.25rem; color: #fff;">{{ $quiz->title }}</h3>
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $quiz->questions_count }} Questions
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Difficulty: {{ ucfirst($quiz->difficulty) }}
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <!-- Export Leaderboard to Excel -->
                        <a href="{{ route('creator.export', $quiz->id) }}" class="btn-glass" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" title="Export Analytics to Excel">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: var(--success);"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Stats
                        </a>

                        <!-- Edit -->
                        <a href="{{ route('creator.edit', $quiz->id) }}" class="btn-glass" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Questions
                        </a>

                        <!-- Delete -->
                        <form action="{{ route('creator.delete', $quiz->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quiz and all its questions?');" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-glass" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; border-color: rgba(239, 68, 68, 0.3); color: var(--danger);">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
