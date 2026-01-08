<!-- Ranking CTA Modal - Simple Call To Action -->
<div class="modal fade" id="rankingCtaModal" tabindex="-1" aria-labelledby="rankingCtaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="border: none; padding: 30px 30px 20px 30px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding: 0 30px 30px 30px; text-align: center;">
                <!-- Icon -->
                <div style="margin-bottom: 20px;">
                    <i class="ph ph-heart-half" style="font-size: 3rem; color: #ff6b9d;"></i>
                </div>

                <!-- Title -->
                <h2 style="margin-bottom: 15px; color: #333; font-weight: 700;">
                    {{ __('Participe da Votação!') }}
                </h2>

                <!-- Message -->
                <p style="margin-bottom: 25px; color: #666; font-size: 1rem; line-height: 1.5;">
                    Parece que você ainda não votou esta semana na creator mais requisitada da comunidade.
                </p>

                <!-- Vote Counter -->
                <div style="background: #f5f5f5; padding: 15px; border-radius: 10px; margin-bottom: 25px;">
                    <p style="margin: 0; color: #999; font-size: 0.9rem;">{{ __('Votos disponíveis neste período') }}</p>
                    <p style="margin: 5px 0 0 0; color: #333; font-size: 1.3rem; font-weight: 700;">
                        <span style="color: #ff6b9d;">{{ $data['votes_remaining'] }}</span> / {{ $data['total_votes_allowed'] }}
                    </p>
                </div>

                <!-- CTA Button -->
                <a href="{{ route('voting.index') }}" class="btn btn-primary" 
                   style="width: 100%; padding: 12px; font-size: 1rem; font-weight: 600; border-radius: 8px; text-decoration: none;">
                    <i class="ph ph-arrow-right me-2"></i>{{ __('Votar Agora') }}
                </a>

                <!-- Info Text -->
                <p style="margin-top: 20px; color: #999; font-size: 0.85rem;">
                    {{ __('Cada usuário pode realizar até 3 votos por período de votação') }}
                </p>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
<script>
    // Show the CTA modal on page load if user hasn't voted yet
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-show the modal
        const ctaModal = new bootstrap.Modal(document.getElementById('rankingCtaModal'), {
            backdrop: 'static',
            keyboard: false
        });
        ctaModal.show();
    });
</script>
@endpush

<style>
    #rankingCtaModal .modal-content {
        background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
    }

    #rankingCtaModal .btn-primary {
        background: linear-gradient(135deg, #ff6b9d 0%, #ff5287 100%);
        border: none;
        transition: all 0.3s ease;
    }

    #rankingCtaModal .btn-primary:hover {
        background: linear-gradient(135deg, #ff5287 0%, #ff3d70 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(255, 107, 157, 0.4);
    }

    @media (max-width: 576px) {
        #rankingCtaModal .modal-body {
            padding: 0 20px 20px 20px;
        }

        #rankingCtaModal .modal-header {
            padding: 20px 20px 15px 20px;
        }

        #rankingCtaModal h2 {
            font-size: 1.5rem;
        }
    }
</style>
