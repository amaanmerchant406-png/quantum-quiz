@extends('layouts.app')

@section('title', 'Launch Quiz')

@section('content')
<div style="max-width: 650px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('creator.index') }}" style="color: var(--cyan); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.25rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Studio
        </a>
    </div>

    <div class="glass-panel" style="box-shadow: 0 0 30px rgba(139, 92, 246, 0.1);">
        <h2 class="tech-font glow-primary" style="font-size: 1.75rem; font-weight: 800; margin-bottom: 2rem;">LAUNCH NEW QUIZ</h2>
        
        <form action="{{ route('creator.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Quiz Title</label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Advanced Laravel Trivia" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-input" placeholder="e.g. Technology & Programming" value="{{ old('category') }}" list="categories-list" required>
                <datalist id="categories-list">
                    <option value="Technology & Programming">
                    <option value="Science & Cosmos">
                    <option value="History & Geography">
                    <option value="Pop Culture & Arts">
                </datalist>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty" class="form-input" required>
                        <option value="easy">Easy</option>
                        <option value="medium" selected>Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Time Limit per Question (seconds)</label>
                    <input type="number" name="time_limit" class="form-input" value="30" min="5" max="300" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label">Description / Mission Brief</label>
                <textarea name="description" class="form-input" rows="4" placeholder="Briefly describe what this quiz covers..." style="resize: vertical;">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn-neon tech-font" style="width: 100%; justify-content: center; padding: 0.9rem;">
                ESTABLISH QUIZ FRAME
            </button>
        </form>
    </div>
</div>
@endsection
