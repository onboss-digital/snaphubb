<div class="modal fade modal-xl" id="rankingModal" tabindex="-1" aria-labelledby="rankingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="ranking-modal-title text-center" id="rankingModalLabel">
                    {{__('placeholder.lbl_ranking_modal_title')}}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-5 col-sm-12 mx-auto">
                    <div class="card-header py-2">
                        <h4 class="text-center">{{$data['title']}}</h4>
                    </div>
                    <div class="card-body pt-2">
                        <div class="mt-2">
                            <div>
                                <div class="d-flex justify-content-center">
                                    {{$data['description']}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-12">
                    <div class="swiper ranking-swiper">
                        <div class="swiper-wrapper h-100 mb-5">
                            @foreach ($data['contents'] as $content)
                                <div class="swiper-slide">
                                    <div class="card model-card"
                                        style="background-image: url('{{$content->image}}'); background-size: cover;">
                                        <div class="card-body text-white d-flex flex-column justify-content-end"
                                            style="background: rgba(0, 0, 0, 0.5);">
                                            <h5 class="card-title mb-2">{{$content->title}}</h5>
                                            <p class="card-text">{{$content->description}}</p>
                                        </div>
                                        <button class="voteButton btn btn-primary cursor-pointer float-end mt-2"
                                            data-ranking-id="{{ $data['id'] }}"
                                            data-content-slug="{{ $content->slug }}">{{ __('placeholder.lbl_ranking_modal_label_vote') }}</button>
                                    </div>

                                </div>
                            @endforeach
                            <div class="swiper-slide">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mt-2">
                                            <div class="">
                                                <h6 class="text-center mb-5">
                                                    {{__('placeholder.lbl_ranking_modal_request')}}
                                                </h6>
                                                {{ html()->label(__('placeholder.lbl_ranking_modal_name') . ' <span class="text-danger">*</span>', 'name')->class('form-label') }}
                                                {{ html()->text('sugestion_name')->id('sugestion-name')->attribute('value')->placeholder(__('placeholder.lbl_ranking_modal_name'))->class('form-control ranking-modal-input')->attribute('required', 'required') }}
                                                <span class="text-danger" id="error_msg"></span>
                                                @error('sugestion_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <div class="invalid-feedback" id="name-error">Name field is required
                                                </div>

                                                {{ html()->label(__('placeholder.lbl_ranking_modal_link') . ' <span class="text-danger">*</span>', 'name')->class('form-label mt-4') }}
                                                {{ html()->text('sugestion_link')->id('sugestion-link')->attribute('value')->placeholder(__('placeholder.lbl_ranking_modal_link'))->class('form-control ranking-modal-input')->attribute('required', 'required') }}
                                                <span class="text-danger" id="error_msg"></span>
                                                @error('sugestion_link')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <div class="invalid-feedback" id="name-error">Name field is required
                                                </div>


                                                <div class="ranking-modal-label-input mt-1">
                                                    {{__('placeholder.lbl_ranking_modal_label_input')}}
                                                </div>
                                            </div>
                                            <div class="end mt-5 mb-1">
                                                <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
                                                    <button
                                                        class="voteButton btn btn-primary cursor-pointer float-end mt-2"
                                                        data-ranking-id="{{ $data['id'] }}"
                                                        data-content-slug="sugestion">{{ __('placeholder.lbl_ranking_modal_label_send') }}</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script>
        var swiper = new Swiper(".ranking-swiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
            },
            mousewheel: true,
            keyboard: true,
        });

        //get screen width

        $(document).ready(function () {
            var screenWidth = $(window).width();
            if (screenWidth < 768) {
                $('#rankingModal').modal('show');
            }
            $('.voteButton').on('click', function () {
                var rankingId = $(this).data('ranking-id');
                var contentSlug = $(this).data('content-slug');

                var sugestionName = $('#sugestion-name').val();
                var sugestionLink = $('#sugestion-link').val();

                console.log(rankingId, contentSlug, sugestionName, sugestionLink);
                $.ajax({
                    url: '{{ route("ranking.vote") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ranking_id: rankingId,
                        content_slug: contentSlug,
                        sugestion_name: sugestionName,
                        sugestion_link: sugestionLink
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#rankingModal').modal('hide');
                    },
                    error: function (response) {
                        alert(response.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush

<style>
    .ranking-modal-title {
        font-size: 2rem;
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
        height: 100%;
    }

    .swiper-pagination-bullet {
        background: white;
    }

    .swiper-slide .model-card {
        height: 430px;
    }

    .card-text {
        font-size: 14px;
    }

    .card-title {
        font-size: 18px;
        font-weight: bold;
    }
</style>