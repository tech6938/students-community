@extends('layouts.app')

@section('title', 'FreelancerHub | Login')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="brand-header">
                <i class="fas fa-code brand-icon"></i>
                <div class="logo">Freelancer<span>Hub</span></div>
            </div>
        </div>
        
        <div class="login-body">
            <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="name@example.com"
                            required
                        />
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        />
                        <span class="input-group-text password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mb-4">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>
            
            <div class="text-center">
                <p class="mb-0">
                    Don't have an account? 
                    <a href="{{ route('signup') }}" class="login-link">Create account</a>
                </p>
            </div>
        </div>
        
        <div class="login-footer">
            <p class="small mb-0">
                By signing in, you agree to our 
                <a href="#" class="login-link">Terms</a> and 
                <a href="#" class="login-link">Privacy Policy</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .login-container {
        width: 100%;
        max-width: 450px;
    }

    .login-card {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(67, 97, 238, 0.15);
        width: 100%;
        overflow: hidden;
    }

    .login-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        padding: 2.5rem 2rem;
        text-align: center;
        color: white;
    }

    .login-header h2 {
        color: white;
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .logo {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    .logo span {
        color: var(--accent-color);
    }

    .login-body {
        padding: 2.5rem 2rem 2rem;
    }

    .login-footer {
        padding: 1.5rem 2rem;
        text-align: center;
        background-color: rgba(248, 249, 250, 0.7);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-right: none;
        color: var(--gray-color);
    }

    .password-toggle {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
        cursor: pointer;
        color: var(--gray-color);
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .btn-primary {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
        width: 100%;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .login-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .login-link:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .brand-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .brand-icon {
        font-size: 2.2rem;
        margin-right: 0.5rem;
        color: var(--accent-color);
    }

    @media (max-width: 768px) {
        .login-header {
            padding: 2rem 1.5rem;
        }
        
        .login-body {
            padding: 2rem 1.5rem 1.5rem;
        }
        
        .logo {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                
                const eyeIcon = this.querySelector("i");
                if (type === "password") {
                    eyeIcon.classList.remove("fa-eye-slash");
                    eyeIcon.classList.add("fa-eye");
                } else {
                    eyeIcon.classList.remove("fa-eye");
                    eyeIcon.classList.add("fa-eye-slash");
                }
            });
        }
    });
</script>
@endpush