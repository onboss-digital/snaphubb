<div class="modal fade modal-xl" id="rankingModal" tabindex="-1" aria-labelledby="rankingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="ranking-modal-title" id="rankingModalLabel">{{__('placeholder.lbl_ranking_modal_title')}}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-5 col-9 mx-auto">
                    <div class="card-body">
                        <div class="mt-2">
                            <div class="">
                                <h6 class="text-center">{{$data['title']}}</h6>
                                <div class="d-flex justify-content-center">
                                    {{$data['description']}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="row h-100">
                            @foreach ($data['contents'] as $content)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100"
                                        style="background-image: url('{{$content->image}}'); background-size: cover;">
                                        <div class="card-body text-white d-flex flex-column justify-content-end"
                                            style="background: rgba(0, 0, 0, 0.5);">
                                            <h5 class="card-title">{{$content->title}}</h5>
                                            <p class="card-text">{{$content->description}}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="card col-3 end">
                        <div class="card-body">
                            <div class="mt-2">
                                <div class="">
                                    <h6 class="text-center mb-5">{{__('placeholder.lbl_ranking_modal_request')}}</h6>
                                    {{ html()->label(__('movie.lbl_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                                    {{ html()->text('name')->attribute('value')->placeholder(__('placeholder.lbl_movie_name'))->class('form-control ranking-modal-input')->attribute('required', 'required') }}
                                    <span class="text-danger" id="error_msg"></span>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback" id="name-error">Name field is required</div>

                                    {{ html()->label(__('movie.lbl_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label mt-4') }}
                                    {{ html()->text('name')->attribute('value')->placeholder(__('placeholder.lbl_movie_name'))->class('form-control ranking-modal-input')->attribute('required', 'required') }}
                                    <span class="text-danger" id="error_msg"></span>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="invalid-feedback" id="name-error">Name field is required</div>


                                    <div class="ranking-modal-label-input mt-1">
                                        {{__('placeholder.lbl_ranking_modal_label_input')}}
                                    </div>
                                </div>
                                <div class="end mt-5 mb-1">
                                    <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
                                        {{ html()->submit(trans('placeholder.lbl_ranking_modal_label_submit'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#rankingModal').modal('show');
    });
</script>

<style>
    .ranking-modal-title {
        font-size: 3rem;
        font-weight: 600;
        margin-left: auto;
    }

    .ranking-modal-input {
        background-color: white;
        color: black;
    }

    .ranking-modal-input:focus {
        border-color: #380707;
        color: black;
        background-color: white;
    }

    .ranking-modal-label-input {
        font-size: 0.8rem;
        font-weight: 200;
        color: #ffffff;
    }

    #rankingModal {
        --bs-modal-width: 1600px;
    }

    #rankingModal .modal-content,
    #rankingModal .modal-dialog {
        height: 94%;
    }
</style>