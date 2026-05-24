@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
    <h1>Profile Settings</h1>
@stop

@section('content')
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            Profile updated successfully.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success">
            Password updated successfully.
        </div>
    @endif

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-info">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Update Profile Information</h3>
                </div>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                            >
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                            >
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Update Password</h3>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                            >
                            @if($errors->updatePassword->has('current_password'))
                                <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                            >
                            @if($errors->updatePassword->has('password'))
                                <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                            >
                            @if($errors->updatePassword->has('password_confirmation'))
                                <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Delete Account</h3>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete your account permanently? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')

                    <div class="card-body">
                        <p class="text-muted">
                            This will permanently delete your account and related data.
                        </p>

                        <div class="form-group">
                            <label for="delete_password">Current Password</label>
                            <input
                                type="password"
                                id="delete_password"
                                name="password"
                                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                required
                            >
                            @if($errors->userDeletion->has('password'))
                                <span class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
