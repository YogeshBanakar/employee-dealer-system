@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $user = auth()->user();
    @endphp
    <div class="row">
        <div class="col-md-8">
            <h3>Dashboard</h3>
            <p class="text-muted">Welcome, <strong>{{ $user->full_name }}</strong>!</p>
        </div>
    </div>
  
    @if($user->isDealer() && !$user->city)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Please complete your location profile to continue.
            <a href="{{ route('dealer.complete-profile') }}" class="btn btn-sm btn-warning ms-2">
                Complete Now
            </a>
        </div>
    @endif
    @if($user->isEmployee())
        <div class="row g-3 mb-4">

            <div class="col-md-4 d-flex">
                <div class="card shadow-sm w-100 h-100 border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-people-fill display-5 text-primary"></i>
                        <h5 class="mt-2 text-muted">Total Users</h5>
                        <h3 class="fw-bold">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex">
                <div class="card shadow-sm w-100 h-100 border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-person-badge display-5 text-success"></i>
                        <h5 class="mt-2 text-muted">Dealers</h5>
                        <h3 class="fw-bold">{{ $totalDealers }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex">
                <div class="card shadow-sm w-100 h-100 border-0 text-center">
                    <div class="card-body">
                        <i class="bi bi-person-check display-5 text-warning"></i>
                        <h5 class="mt-2 text-muted">Employees</h5>
                        <h3 class="fw-bold">{{ $totalEmployees }}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row mt-3 g-3">
        <div class="col-md-4 d-flex">
            <div class="card shadow text-center w-100 h-100">
                <div class="card-body">
                    <i class="bi bi-person-circle display-4 text-primary"></i>
                    <h5 class="mt-2">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    <span class="badge bg-{{ $user->isDealer() ? 'info' : 'secondary' }}">
                        {{ ucfirst($user->user_type) }}
                    </span>
                </div>
            </div>
        </div>

        @if($user->isEmployee())
            <div class="col-md-4 d-flex">
                <div class="card shadow text-center w-100 h-100">
                    <div class="card-body">
                        <i class="bi bi-people display-4 text-warning"></i>
                        <h5 class="mt-2">Manage Users</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm mt-1">
                            View All Users
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
    </div>
@endsection
