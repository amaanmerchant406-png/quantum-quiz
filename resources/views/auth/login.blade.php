@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 70vh;">
    <div class="glass-panel" style="width: 100%; max-width: 450px; box-shadow: 0 0 40px rgba(139, 92, 246, 0.15);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 class="tech-font glow-primary" style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem;">ACCESS PORTAL</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Enter your credentials to board the platform.</p>
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

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Command Email</label>
                <input type="email" name="email" class="form-input" placeholder="commander@quantum.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="margin-bottom: 0;">Security Passcode</label>
                </div>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
                <input type="checkbox" name="remember" id="remember" style="accent-color: var(--primary); cursor: pointer; width: 16px; height: 16px;">
                <label for="remember" style="color: var(--text-muted); font-size: 0.85rem; cursor: pointer; user-select: none;">Keep session open</label>
            </div>

            <button type="submit" class="btn-neon tech-font" style="width: 100%; justify-content: center; padding: 0.9rem;">
                ESTABLISH CONNECTION
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                New explorer? 
                <a href="{{ route('register') }}" style="color: var(--cyan); text-decoration: none; font-weight: 600;" class="glow-cyan">Create Account</a>
            </p>
        </div>
    </div>
</div>
@endsection
