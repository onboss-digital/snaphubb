@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }} - {{ $plan->name }}
@endsection

@section('content')
<x-back-button-component route="backend.plans.index" />

<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">
                <span class="badge bg-blue-lt">{{ $plan->name }}</span>
                <small class="text-muted ms-2">Plan Limitations Management</small>
            </h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-list"></i> Assigned Limitations
                    <span class="badge bg-info ms-2">{{ $limitations->count() }} / {{ $allLimitations->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                @if($limitations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th width="40%">
                                        <i class="fa-solid fa-tag"></i> Limitation
                                    </th>
                                    <th width="30%" class="text-center">Status</th>
                                    <th width="30%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($limitations as $limitation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="font-weight-medium">
                                                        {{ $limitation->limitation_data->title ?? $limitation->limitation_slug }}
                                                    </div>
                                                    <small class="text-muted">{{ $limitation->limitation_slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(in_array($limitation->limitation_data->slug ?? '', ['video-cast', 'ads', 'download-status']))
                                                <span class="badge {{ $limitation->limit == 1 ? 'bg-success' : 'bg-danger' }}">
                                                    <i class="fa-solid {{ $limitation->limit == 1 ? 'fa-check' : 'fa-xmark' }}"></i>
                                                    {{ $limitation->limit == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            @else
                                                <code class="bg-light px-2 py-1 rounded">{{ $limitation->limit ?? 'N/A' }}</code>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('backend.planlimitation_mapping.destroy', $limitation->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this limitation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-header">{{ __('messages.no_data') }}</div>
                        <p class="empty-title">No limitations assigned yet</p>
                        <p class="empty-subtitle text-muted">Add limitations from the panel on the right to set plan restrictions.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa-solid fa-plus"></i> Add Limitation
                </h3>
            </div>
            <div class="card-body">
                @php
                    $availableLimitations = $allLimitations->filter(function($lim) use ($limitations) {
                        return !$limitations->where('planlimitation_id', $lim->id)->count();
                    });
                @endphp

                @if($availableLimitations->count() > 0)
                    <form method="POST" action="{{ route('backend.planlimitation_mapping.store_limitation', $plan->id) }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="limitation" class="form-label">Select Limitation</label>
                            <select name="planlimitation_id" id="limitation" class="form-control" required>
                                <option value="">-- Choose a limitation --</option>
                                @foreach($availableLimitations as $lim)
                                    <option value="{{ $lim->id }}">
                                        <i class="fa-solid fa-check"></i> {{ $lim->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('planlimitation_id')
                                <span class="text-danger small d-block mt-1">
                                    <i class="fa-solid fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="limit_value" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="limit" id="limit_value" value="1" checked>
                                <label class="form-check-label" for="limit_value">
                                    Enable this limitation
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-plus"></i> Add Limitation
                        </button>
                    </form>
                @else
                    <div class="alert alert-success alert-icon" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            </div>
                            <div>
                                <h4 class="alert-title">All Set!</h4>
                                <div class="text-muted">All available limitations are already assigned to this plan.</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.insertBefore(alert, document.body.firstChild);
        setTimeout(() => alert.remove(), 5000);
    });
</script>
@endif
@endsection
