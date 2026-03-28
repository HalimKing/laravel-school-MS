@extends('layouts.app')

@section('title', 'Profile')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .profile-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #667eea;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        color: #333;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 5px;
        word-break: break-word;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        background: #667eea;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #764ba2;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #e9ecef;
        color: #333;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #d9dfe4;
    }

    .alert {
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .error-message {
        color: #721c24;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-strength {
        margin-top: 0.5rem;
        padding: 0.5rem;
        border-radius: 3px;
        font-size: 0.875rem;
        display: none;
    }

    .password-strength.weak {
        background: #f8d7da;
        color: #721c24;
        display: block;
    }

    .password-strength.medium {
        background: #fff3cd;
        color: #856404;
        display: block;
    }

    .password-strength.strong {
        background: #d4edda;
        color: #155724;
        display: block;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .profile-section {
            padding: 1.5rem;
        }
    }

    .role-badge {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .password-toggle {
        cursor: pointer;
        user-select: none;
    }

    .divider {
        height: 1px;
        background: #f0f0f0;
        margin: 2rem 0;
    }
</style>
@endpush

@section('content')
<!-- Profile Header -->
<div class="profile-header">
    <div class="d-flex align-items-center">
        <div class="profile-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div style="flex: 1;">
            <h2 style="margin: 0; margin-bottom: 0.5rem;">{{ Auth::user()->name }}</h2>
            <p style="margin: 0; opacity: 0.9;">
                <i data-lucide="mail" class="icon-sm"></i>
                {{ Auth::user()->email }}
            </p>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if($errors->any())
<div class="alert alert-danger">
    <strong>⚠️ Please fix the following errors:</strong>
    <ul style="margin-bottom: 0; margin-top: 0.5rem;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div class="alert alert-success">
    <strong>✅ Success!</strong> {{ session('success') }}
</div>
@endif

<!-- Profile Information Section -->
<div class="profile-section">
    <div class="section-title">
        <i data-lucide="user" style="width: 20px; height: 20px;"></i>
        Profile Information
    </div>

        <div class="form-row">
            <!-- Full Name -->
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <p
                    class="form-control"
                    id="name"
                    name="name"
                    required>
                    {{ Auth::user()->name }}
                </p>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <p
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value=""
                    required>
                    {{  Auth::user()->email }}
                </p>
               
            </div>
        </div>
</div>

<!-- Account Information Section -->
<div class="profile-section">
    <div class="section-title">
        <i data-lucide="shield" style="width: 20px; height: 20px;"></i>
        Account Information
    </div>

    <div class="form-row">
        <!-- Role -->
        <div class="info-group">
            <div class="info-label">👤 Role(s)</div>
            <div>
                @if($roles && count($roles) > 0)
                @foreach($roles as $role)
                <span class="role-badge">{{ ucfirst($role) }}</span>
                @endforeach
                @else
                <div class="info-value">No roles assigned</div>
                @endif
            </div>
        </div>

        <!-- Account Status -->
        <div class="info-group">
            <div class="info-label">📊 Account Status</div>
            <div class="info-value">
                @if(Auth::user()->is_active)
                <span style="color: #28a745;">✓ Active</span>
                @else
                <span style="color: #dc3545;">✗ Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <div class="form-row">
        <!-- Created At -->
        <div class="info-group">
            <div class="info-label">📅 Account Created</div>
            <div class="info-value">{{ Auth::user()->created_at->format('M d, Y \a\t h:i A') }}</div>
        </div>

        <!-- Last Updated -->
        <div class="info-group">
            <div class="info-label">🔄 Last Updated</div>
            <div class="info-value">{{ Auth::user()->updated_at->format('M d, Y \a\t h:i A') }}</div>
        </div>
    </div>
</div>

<!-- Password Management Section -->
<div class="profile-section">
    <div class="section-title">
        <i data-lucide="lock" style="width: 20px; height: 20px;"></i>
        Password Management
    </div>

    <div class="alert alert-warning">
        <strong>🔒 Security Notice:</strong> Keep your password secure and change it regularly. Never share your password with anyone.
    </div>

    <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf
        @method('PUT')

        <!-- Current Password -->
        <div class="form-group">
            <label for="current_password" class="form-label">Current Password</label>
            <div style="position: relative;">
                <input type="password"
                    class="form-control @error('current_password') is-invalid @enderror"
                    id="current_password"
                    name="current_password"
                    placeholder="Enter your current password"
                    required>
                <i data-lucide="eye" class="password-toggle" onclick="togglePassword('current_password')"
                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 18px; height: 18px;"></i>
            </div>
            @error('current_password')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="divider"></div>

        <!-- New Password -->
        <div class="form-row">
            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <div style="position: relative;">
                    <input type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        onkeyup="checkPasswordStrength(this.value)">
                    <i data-lucide="eye" class="password-toggle" onclick="togglePassword('password')"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 18px; height: 18px;"></i>
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    • At least 8 characters<br>
                    • Must contain uppercase, lowercase, number, and special character
                </small>
                @error('password')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password"
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                        required>
                    <i data-lucide="eye" class="password-toggle" onclick="togglePassword('password_confirmation')"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 18px; height: 18px;"></i>
                </div>
                @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">
                <i data-lucide="lock" style="width: 16px; height: 16px; display: inline; margin-right: 0.5rem;"></i>
                Update Password
            </button>
            <button type="reset" class="btn-secondary">
                <i data-lucide="x" style="width: 16px; height: 16px; display: inline; margin-right: 0.5rem;"></i>
                Cancel
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const isPassword = field.type === 'password';
        field.type = isPassword ? 'text' : 'password';
    }

    // Check password strength
    function checkPasswordStrength(password) {
        const strengthDisplay = document.getElementById('passwordStrength');

        if (!password) {
            strengthDisplay.style.display = 'none';
            return;
        }

        let strength = 0;

        // Check for minimum length
        if (password.length >= 8) strength++;

        // Check for uppercase
        if (/[A-Z]/.test(password)) strength++;

        // Check for lowercase
        if (/[a-z]/.test(password)) strength++;

        // Check for numbers
        if (/[0-9]/.test(password)) strength++;

        // Check for special characters
        if (/[!@#$%^&*]/.test(password)) strength++;

        // Remove previous classes
        strengthDisplay.classList.remove('weak', 'medium', 'strong');

        if (strength < 3) {
            strengthDisplay.textContent = '⚠️ Weak Password - Please use a stronger password';
            strengthDisplay.className = 'password-strength weak';
        } else if (strength < 4) {
            strengthDisplay.textContent = '⚡ Medium Strength - Could be stronger';
            strengthDisplay.className = 'password-strength medium';
        } else {
            strengthDisplay.textContent = '✅ Strong Password - Well done!';
            strengthDisplay.className = 'password-strength strong';
        }
    }

    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#dc3545';
                } else {
                    input.style.borderColor = '#ddd';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endpush