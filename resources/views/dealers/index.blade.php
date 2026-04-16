@extends('layouts.app')

@section('title', 'Dealers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-shop"></i> Dealer List</h3>
</div>

{{-- Zip Code Search --}}
<div class="card shadow mb-3">
    <div class="card-body">
        <form action="{{ route('dealers.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="zip_code" class="form-label fw-bold">Search by Zip Code</label>
                <input type="text" class="form-control" id="zip_code" name="zip_code"
                       value="{{ request('zip_code') }}" placeholder="Enter zip code...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
            @if(request('zip_code'))
                <div class="col-auto">
                    <a href="{{ route('dealers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

@if(request('zip_code'))
    <p class="text-muted">Showing results for zip code: <strong>{{ request('zip_code') }}</strong></p>
@endif

{{-- Dealer Table --}}
<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dealers as $dealer)
                    <tr>
                        <td>{{ $dealer->id }}</td>
                        <td>{{ $dealer->full_name }}</td>
                        <td>{{ $dealer->email }}</td>
                        <td>{{ $dealer->city ?? '—' }}</td>
                        <td>{{ $dealer->state ?? '—' }}</td>
                        <td>{{ $dealer->zip_code ?? '—' }}</td>
                        <td>
                            <a href="{{ route('dealers.edit-location', $dealer) }}" class="btn btn-sm btn-warning" title="Edit Location">
                                <i class="bi bi-geo-alt"></i> Edit Location
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No dealers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $dealers->links() }}
</div>
@endsection
