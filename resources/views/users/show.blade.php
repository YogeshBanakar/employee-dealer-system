@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="bi bi-person"></i> User Details</h4>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $user->id }}</dd>

                    <dt class="col-sm-4">First Name</dt>
                    <dd class="col-sm-8">{{ $user->first_name }}</dd>

                    <dt class="col-sm-4">Last Name</dt>
                    <dd class="col-sm-8">{{ $user->last_name }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4">User Type</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $user->isDealer() ? 'info' : 'secondary' }}">
                            {{ ucfirst($user->user_type) }}
                        </span>
                    </dd>

                    @if($user->isDealer())
                        <dt class="col-sm-4">City</dt>
                        <dd class="col-sm-8">{{ $user->city ?? '—' }}</dd>

                        <dt class="col-sm-4">State</dt>
                        <dd class="col-sm-8">{{ $user->state ?? '—' }}</dd>

                        <dt class="col-sm-4">Zip Code</dt>
                        <dd class="col-sm-8">{{ $user->zip_code ?? '—' }}</dd>
                    @endif

                    <dt class="col-sm-4">Registered</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('M d, Y h:i A') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
                </form>
                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm ms-auto">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
