@extends('layouts.app')

@section('title', 'Join Mission')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 80vh;">
    <div class="glass-panel" style="width: 100%; max-width: 500px; box-shadow: 0 0 40px rgba(6, 182, 212, 0.15);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 class="tech-font glow-cyan" style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem;">CREATE ACCOUNT</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Join the quantum network to test your limits.</p>
        </div>

        @if ($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; color: #ff8080;">
                <ul style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Explorer Callsign (Name)</label>
                <input type="text" name="name" class="form-input" placeholder="Aria Stark" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Command Email</label>
                <input type="email" name="email" class="form-input" placeholder="aria@quantum.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Sector Role</label>
                <select name="role" class="form-input" required>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Explorer (Take Quizzes & Rank Up)</option>
                    <option value="creator" {{ old('role') == 'creator' ? 'selected' : '' }}>Creator (Build Quizzes & View Analytics)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Security Passcode</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label">Confirm Security Passcode</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-neon btn-neon-magenta tech-font" style="width: 100%; justify-content: center; padding: 0.9rem;">
                INITIALIZE ENROLLMENT
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Already verified? 
                <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;" class="glow-primary">Access Portal</a>
            </p>
        </div>
    </div>
</div>
@endsection
