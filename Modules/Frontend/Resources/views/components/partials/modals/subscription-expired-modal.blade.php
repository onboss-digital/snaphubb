<div class="modal fade modal-lg" id="subscriptionExpiredModal" tabindex="-1"
    aria-labelledby="subscriptionExpiredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card my-4 col-sm-10 mx-auto">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="ph ph-crown text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="card-title mb-3">{{ __('placeholder.lbl_subscription_expired_modal_message') }}</h5>

                        <div class="subscription-details mb-4">
                            <p class="mb-2">
                                <strong>{{ __('placeholder.lbl_subscription_expired_modal_plan_name') }}:</strong>
                                {{ $data['subscription_name'] }}
                            </p>
                            <p class="mb-2">
                                <strong>{{ __('placeholder.lbl_subscription_expired_modal_expired_on') }}:</strong>
                                {{ $data['end_date'] }}
                            </p>
                            @if ($data['days_expired'] > 0)
                                <p class="text-danger mb-0">
                                    {{ __('placeholder.lbl_subscription_expired_modal_days_ago', ['days' => $data['days_expired']]) }}
                                </p>
                            @endif
                        </div>

                        <div class="subscription-benefits mb-4">
                            <h6 class="mb-3">{{ __('placeholder.lbl_subscription_expired_modal_benefits_title') }}
                            </h6>
                            <ul class="list-unstyled text-start">
                                <li class="mb-2"><i
                                        class="ph ph-check-circle text-success me-2"></i>{{ __('placeholder.lbl_subscription_expired_modal_benefit_1') }}
                                </li>
                                <li class="mb-2"><i
                                        class="ph ph-check-circle text-success me-2"></i>{{ __('placeholder.lbl_subscription_expired_modal_benefit_2') }}
                                </li>
                                <li class="mb-2"><i
                                        class="ph ph-check-circle text-success me-2"></i>{{ __('placeholder.lbl_subscription_expired_modal_benefit_3') }}
                                </li>
                                <li class="mb-2"><i
                                        class="ph ph-check-circle text-success me-2"></i>{{ __('placeholder.lbl_subscription_expired_modal_benefit_4') }}
                                </li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('subscriptionPlan') }}" class="btn btn-primary btn-lg px-4">
                                {{__('placeholder.lbl_subscription_expired_modal_renew_button')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script>
        $(document).ready(function() {

            // Impedir que o modal seja fechado clicando fora dele (opcional)
            $('#subscriptionExpiredModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#subscriptionExpiredModal').modal('show');
        });
    </script>
@endpush

<style>
    .subscription-expired-modal-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #dc3545;
    }

    @media (max-width: 767px) {
        .subscription-expired-modal-title {
            font-size: 1.5rem;
        }
    }

    #subscriptionExpiredModal .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 0 50px rgba(220, 38, 38, 0.3);
    }

    #subscriptionExpiredModal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }

    #subscriptionExpiredModal .btn-close {
        color: white;
        opacity: 0.8;
    }

    #subscriptionExpiredModal .btn-close:hover {
        opacity: 1;
    }

    .subscription-details {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #dc3545;
        color: black;
    }

    .subscription-benefits {
        background-color: #e7f3ff;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #0066cc;
        color: black;
    }

    .subscription-benefits h6 {
        font-size: 1.25rem;
        font-weight: 500;
        color: #dc3545;
    }

    @media (max-width: 767px) {
        #subscriptionExpiredModal .modal-dialog {
            margin: 1rem;
        }

        .d-grid.gap-2.d-md-flex {
            flex-direction: column;
        }

        .btn-lg {
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }
    }
</style>
