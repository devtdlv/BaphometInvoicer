@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">Login</h1>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
                @error('password')
                    <p style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin: 0;">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
            Don't have an account? <a href="{{ route('register') }}" style="color: var(--accent);">Register</a>
        </p>
    </div>
</div>
@endsection

