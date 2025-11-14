@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">Register</h1>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <p style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
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
            
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="is_client" id="is_client" value="1">
                <label for="is_client" style="margin: 0;">Register as client</label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
            Already have an account? <a href="{{ route('login') }}" style="color: var(--accent);">Login</a>
        </p>
    </div>
</div>
@endsection

