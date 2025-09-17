@extends('backend.layouts.app')

@section('content')
    <x-back-button-component route="backend.users.ranking.index_list" />
    {{ html()->form('PUT', route('backend.users.ranking.update', $ranking->id))
        ->attribute('enctype', 'multipart/form-data')
        ->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')
        ->class('requires-validation')
        ->attribute('novalidate', 'novalidate')
        ->open()
        }}
    <div class="card">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-xl-12">
                    <div class="row gy-3">
                        <div class="col-md-12 col-lg-12 row">
                            <div class="col-md-6 col-lg-6 mb-3">
                                {{ html()->label(__('Name') . '<span class="text-danger">*</span>', 'name')->class('form-label')}}
                                {{
        html()->text('name', $ranking->name)
            ->class('form-control')
            ->id('name')
            ->placeholder(__('Ranking name'))
            ->attribute('required', 'required')
                                    }}
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="name-error">Name field is required</div>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                {{ html()->label(__('plan.lbl_status'), 'status')->class('form-label') }}
                                <div class="d-flex justify-content-between align-items-center form-control">
                                    {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0') }}
                                    <div class="form-check form-switch">
                                        {{ html()->hidden('status', 0) }}
                                        {{
        html()->checkbox('status', $ranking->status)
            ->class('form-check-input')
            ->id('status')
            ->value(1)
                                            }}
                                    </div>
                                </div>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2 col-lg-2">
                                {{ html()->label(__('Start date') . '<span class="text-danger">*</span>', 'start_date')->class('form-label') }}
                                {{ html()->text('start_date', $ranking->start_date)->placeholder(__('Start date'))->class('form-control datetimepicker')->attribute('required', 'required')->id('start_date') }}
                                @error('start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div class="invalid-feedback" id="start_date-error">Start Date field is required</div>
                            </div>
                            <div class="col-md-2 col-lg-2">
                                {{ html()->label(__('End date'), 'end_date')->class('form-label') }}
                                {{ html()->text('end_date', $ranking->end_date)->placeholder(__('End date'))->class('form-control datetimepicker')->id('end_date') }}
                                @error('end_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            {{ html()->label(__('plan.lbl_description') . ' <span class="text-danger">*</span>', 'description')->class('form-label') }}
                            {{
        html()->textarea('description', $ranking->description)
            ->class('form-control')
            ->id('description')
            ->placeholder(__('Ranking description'))
            ->rows('5')
            ->attribute('required', 'required')
                                }}
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback" id="description-error">Description field is required</div>
                        </div>
                        <div class="col-md-6">
                            @php
                            // dd($ranking->plans->pluck('id')->toArray(), $plans);
                            @endphp


                            {{ html()->label(__('Plans')  . '<span class="text-danger">*</span>', 'plans')->class('form-label') }}
                            {{ html()->select('plans[]', $plans, $ranking->plans->pluck('id')->toArray())->class('form-control select2')->id('plans')->multiple()->attribute('required','required') }}
                            @error('plans')
                               <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="invalid-feedback" id="plans-error">Plans field is required</div>
                        </div>
                       
                    </div>
                    <div class="row gy-1">
                        @for ($i = 1; $i <= 3; $i++)
                            <x-backend.ranking.content :ranking="$ranking" :index="$i" />
                        @endfor
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title
            ">{{ __('Sugestions') }}</h4>
        </div>
        <div class="card-body">
            
            {{-- table sugestions --}}
            <div class="col">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Link') }}</th>
                            <th>{{ __('User') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ranking->sugestions as $sugestion)
                            <tr>
                                <td>{{ $sugestion->sugestion_name }}</td>
                                <td>{{ $sugestion->sugestion_link }}</td>
                                <td>{{ $sugestion->user->first_name }} {{ $sugestion->user->last_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
        <button type="button" class="btn btn-md btn-secondary" onclick="resetRankingResponses({{ $ranking->id }})">{{ __('Reset Responses') }}</button>
        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
    </div>
    {{ html()->form()->close() }}
    @include('components.media-modal')
    <script>
        function removeImage(hiddenInputId, removedFlagId) {
            var container = document.getElementById('selectedImageContainer1');
            var hiddenInput = document.getElementById(hiddenInputId);
            var removedFlag = document.getElementById(removedFlagId);

            container.innerHTML = '';
            hiddenInput.value = '';
            removedFlag.value = 1;
        }

        function resetRankingResponses(rankingId) {
            if (confirm('{{ __('Are you sure you want to reset the responses?') }}')) {
                window.location.href = '{{ route('backend.users.ranking.reset_responses', '') }}/' + rankingId;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.min-datetimepicker-time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
            flatpickr('.datetimepicker', {
                dateFormat: "Y-m-d"
            });
        });
    </script>
@endsection