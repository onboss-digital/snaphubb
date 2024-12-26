@extends('backend.layouts.app')

@section('content')

        <div class="row mb-5">
            @include('setting::backend.setting.sidebar-panel')
            @include('setting::backend.setting.main-content')
        </div>

 @if(session('success'))
<div class="snackbar" id="snackbar">

    <div class="d-flex justify-content-around align-items-center">
        <p class="mb-0">{{ session('success') }}</p>
        <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">Dismiss</a>
    </div>
</div>
@endif

@push('after-scripts')
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>
<script src="{{ asset('js/form/index.js') }}" defer></script>

@endpush


@endsection


