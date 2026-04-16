@extends('layouts.app')

@section('title', 'Edit Dealer Location')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-geo-alt"></i> Edit Location: {{ $dealer->full_name }}</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-light border">
                    <strong>Dealer:</strong> {{ $dealer->full_name }}<br>
                    <strong>Email:</strong> {{ $dealer->email }}
                </div>

                <form id="editLocationForm" action="{{ route('dealers.update-location', $dealer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                               id="city" name="city" value="{{ old('city', $dealer->city) }}" required>
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control @error('state') is-invalid @enderror"
                               id="state" name="state" value="{{ old('state', $dealer->state) }}" required>
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                               id="zip_code" name="zip_code" value="{{ old('zip_code', $dealer->zip_code) }}" required>
                        @error('zip_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">Update Location</button>
                        @if(Auth::user()->isEmployee())
                            <a href="{{ route('dealers.index') }}" class="btn btn-secondary">Cancel</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initAjaxForm('editLocationForm', {
            city:     ['required'],
            state:    ['required'],
            zip_code: ['required'],
        });
    });
</script>
@endsection
