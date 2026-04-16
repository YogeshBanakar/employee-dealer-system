@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register</h4>
            </div>
            <div class="card-body p-4">
                <form id="registerForm" action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                               id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                               id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type of User</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type"
                                   id="type_employee" value="employee" {{ old('user_type') == 'employee' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_employee">Employee</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type"
                                   id="type_dealer" value="dealer" {{ old('user_type') == 'dealer' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_dealer">Dealer</label>
                        </div>
                        @error('user_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initAjaxForm('registerForm', {
            first_name:            ['required'],
            last_name:             ['required'],
            email:                 ['required', 'email'],
            password:              ['required', 'min:6'],
            password_confirmation: ['required', 'match:password'],
            user_type:             ['required'],
        });

        initEmailUniquenessCheck('registerForm', '{{ route("check-email") }}');
    });
</script>
@endsection
