@extends('backend.layouts.app')

@section('title')
    {{ __($module_action) }} {{ __($module_title) }}
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/subscriptions/style.css') }}">
@endpush

@section('content')
<div class="card-main mb-5">
    <x-backend.section-header>
        <div class="d-flex flex-wrap gap-3">
            <div>
                <button type="button" class="btn btn-dark" data-modal="export">
                    <i class="ph ph-export align-middle"></i> {{ __('messages.export') }}
                </button>
            </div>
        </div>

        <x-slot name="toolbar">
            <div>
                <div class="datatable-filter">
                    <select name="plan_id" id="plan_id" class="select2 form-control" data-filter="select" style="width: 100%">
                        <option value="">{{ __('messages.all') }} Plans</option>
                        @foreach(\Modules\Subscriptions\Models\Plan::where('status', 1)->get() as $plan)
                            <option value="{{ $plan->id }}" {{ $filter['plan_id'] == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="input-group flex-nowrap">
                <span class="input-group-text pe-0" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..." aria-label="Search" aria-describedby="addon-wrapping">
            </div>
        </x-slot>
    </x-backend.section-header>

    <table id="datatable" class="table table-responsive">
    </table>
</div>

@if(session('success'))
<div class="snackbar" id="snackbar">
    <div class="d-flex justify-content-around align-items-center">
        <p class="mb-0">{{ session('success') }}</p>
        <a href="#" class="dismiss-link text-decoration-none text-success" onclick="dismissSnackbar(event)">Dismiss</a>
    </div>
</div>
@endif
@endsection

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script type="text/javascript" defer>

const columns = [
    {
        name: 'check',
        data: 'check',
        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
        width: '5%',
        exportable: false,
        orderable: false,
        searchable: false,
    },
    {
        data: 'plan_name',
        name: 'plan_name',
        title: "{{ __('plan.lbl_name') }}",
        orderable: true,
        searchable: true,
    },
    {
        data: 'limitation_title',
        name: 'limitation_title',
        title: "{{ __('plan_limitation.lbl_title') }}",
        orderable: true,
        searchable: true,
    },
    {
        data: 'limitation_value',
        name: 'limitation_value',
        title: "{{ __('messages.lbl_value') }}",
        orderable: false,
        searchable: false,
    },
    {
        data: 'status',
        name: 'status',
        title: "{{ __('messages.lbl_status') }}",
        orderable: false,
        searchable: false,
        width: '10%',
    },
];

const actionColumn = [
    {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        title: '{{ __('messages.action') }}',
        width: '10%'
    }
];

let finalColumns = [...columns, ...actionColumn];

document.addEventListener('DOMContentLoaded', (event) => {
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("backend.$module_name.index_data") }}',
            data: function (d) {
                d.plan_id = $('#plan_id').val();
            }
        },
        columns: finalColumns,
        order: [[1, 'desc']],
        dom: 'Blfrtip',
        buttons: [],
        pageLength: 25,
    });

    // Filter by plan_id
    $('#plan_id').on('change', function() {
        table.draw();
    });

    // Filter by search
    $('.dt-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Toggle status
    $(document).on('change', '.status-toggle', function() {
        let id = $(this).data('id');
        $.post('{{ route("backend.$module_name.toggle_status") }}', {
            _token: '{{ csrf_token() }}',
            id: id
        }, function(response) {
            if (response.status === 'success') {
                // Optional: show success message
            }
        });
    });
});

</script>
@endpush
