@extends('frontend::layouts.master')

@section('content')
<script>
    // Translation strings for JavaScript
    window.votingTranslations = {
        votingSuccess: "{{ __('voting::voting.vote_success') }}",
        votingError: "{{ __('voting::voting.vote_error') }}",
        suggestionSuccess: "{{ __('voting::voting.suggestion_success') }}",
        suggestionError: "{{ __('voting::voting.suggestion_error') }}",
        voting: "{{ __('voting::voting.vote_button') }}",
        votes: "{{ __('voting::voting.votes') }}",
        noActiveRanking: "{{ __('voting::voting.no_active_ranking') }}",
        noCandidates: "{{ __('voting::voting.no_candidates') }}",
        noVotesYet: "{{ __('voting::voting.no_votes_yet') }}",
        votingLimit: "{{ __('voting::voting.voting_limit') }}"
    };
</script>
<div class="voting-page pt-4 pb-4">
    <div class="container">
        <!-- Header -->
        <div class="voting-header mb-4">
            <div class="row align-items-center mb-3">
                <div class="col-12">
                    <h1 class="voting-title mb-2">
                        <i class="ph ph-heart-half"></i> {{ __('voting::voting.voting_title') }}
                    </h1>
                    <p class="voting-subtitle text-muted">
                        {{ __('voting::voting.voting_subtitle') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Access Denied Modal -->
        @if(!$hasAccess)
        <div id="accessDeniedModal" class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="accessDeniedLabel" aria-hidden="false" style="display: block; background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content voting-modal">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title" id="accessDeniedLabel">
                            <i class="ph ph-lock text-warning me-2"></i> Acesso Restrito
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-4">
                        <div class="text-center mb-4">
                            <i class="ph ph-lock-key display-6 text-warning mb-3" style="font-size: 4rem;"></i>
                        </div>

                        <h3 class="text-center mb-3">Desbloqueie a Votação da Comunidade</h3>

                        <p class="text-center text-muted mb-4">
                            A Votação da Comunidade é uma feature exclusiva que permite você participar das votações semanais e ajudar a escolher as atrizes mais favoritas da plataforma.
                        </p>

                        <div class="voting-benefits mb-4">
                            <div class="benefit-item d-flex align-items-start mb-3">
                                <i class="ph ph-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Vote Ilimitadamente</strong>
                                    <p class="text-muted mb-0 small">Participe de todas as votações semanais</p>
                                </div>
                            </div>

                            <div class="benefit-item d-flex align-items-start mb-3">
                                <i class="ph ph-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Veja o Top 3 da Semana</strong>
                                    <p class="text-muted mb-0 small">Acompanhe as atrizes mais votadas em tempo real</p>
                                </div>
                            </div>

                            <div class="benefit-item d-flex align-items-start">
                                <i class="ph ph-check-circle text-success me-3 mt-1"></i>
                                <div>
                                    <strong>Influencie a Comunidade</strong>
                                    <p class="text-muted mb-0 small">Seu voto contribui para o ranking global</p>
                                </div>
                            </div>
                        </div>

                        <div class="voting-pricing mb-4">
                            <p class="text-center text-muted small mb-0">
                                Apenas um pequeno investimento para desfrutar de toda essa experiência
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-outline-secondary flex-grow-1 me-2" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary btn-purchase flex-grow-1" onclick="window.location.href='/checkout?product=voting'">
                            <i class="ph ph-credit-card me-2"></i> Comprar Acesso
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade" id="accessDeniedBackdrop" style="display: none;" onclick="document.getElementById('accessDeniedModal').style.display='none'; document.getElementById('accessDeniedBackdrop').style.display='none';"></div>
        @else
        <!-- Content for users with access -->
        <div class="voting-content">
            <!-- Top 3 Section -->
            <div class="top-3-section mb-5">
                <h2 class="section-title mb-5">
                    <i class="ph ph-crown text-warning"></i> {{ __('voting::voting.top_3_section') }}
                </h2>

                <div class="layout-medaloes" id="top3Container">
                    <!-- Carregando... -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4" style="margin-top: 3rem; margin-bottom: 2rem;">

            <!-- Suggestions Section -->
            <div class="suggestions-section" style="margin-top: 2rem;">
                <h2 class="section-title mb-4">
                    <i class="ph ph-target"></i> {{ __('voting::voting.suggest_creator') }}
                </h2>

                <div class="suggest-section">
                    <p class="suggest-description">
                        {{ __('voting::voting.suggest_description') }}
                    </p>

                    <form id="suggestionForm" class="suggestion-form">
                        <div class="form-group">
                            <label for="suggestionName">{{ __('voting::voting.creator_name') }} <span class="required">*</span></label>
                            <input type="text" class="form-input" id="suggestionName" 
                                   name="sugestion_name" placeholder="{{ __('voting::voting.creator_name_placeholder') }}" 
                                   maxlength="255" required>
                            <small class="form-hint">Máximo 255 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="suggestionLink">{{ __('voting::voting.social_link') }} <span class="required">*</span></label>
                            <input type="url" class="form-input" id="suggestionLink" 
                                   name="sugestion_link" placeholder="{{ __('voting::voting.social_link_placeholder') }}" required>
                            <small class="form-hint">Cole o link do site ou rede social</small>
                        </div>

                        <button type="submit" class="suggest-button">
                            <i class="ph ph-paper-plane"></i> {{ __('voting::voting.submit_suggestion') }}
                        </button>

                        <div id="suggestionMessage" class="suggestion-message" style="display: none;">
                            <div class="alert" role="alert"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de Limite de Votos -->
<div id="votingLimitModal" class="voting-limit-modal">
    <div class="voting-limit-modal-content">
        <div class="voting-limit-modal-header">
            <i class="ph ph-info"></i>
            <span id="votingLimitTitle">Limite de Votos Atingido</span>
        </div>
        <div class="voting-limit-modal-body">
            <p id="votingLimitMessage">Você atingiu o limite de 3 votos para este período de votação</p>
        </div>
        <div class="voting-limit-modal-footer">
            <button id="votingLimitButton" class="voting-limit-modal-button" type="button" onclick="window.closeVotingLimitModal()">Entendido</button>
        </div>
    </div>
</div>

<!-- Styles -->
@push('after-styles')
<style>
    /* Esconder banner na página de votação */
    .subscription-status-banner {
        display: none !important;
    }

    /* Garantir que o header fica SEMPRE no topo e visível */
    header.header-center-home,
    header.header-default,
    header.header-sticky,
    header.header-one {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1050 !important;
        width: 100% !important;
    }

    /* Adicionar margin-top ao conteúdo para compensar o header fixo */
    body {
        padding-top: 0;
    }
    
    .voting-page {
        padding-top: 320px;
    }
    
    @media (max-width: 1024px) {
        .voting-page {
            padding-top: 290px;
        }
    }
    
    @media (max-width: 768px) {
        .voting-page {
            padding-top: 280px;
        }
    }
    
    @media (max-width: 576px) {
        .voting-page {
            padding-top: 350px;
        }
    }

    @media (max-width: 420px) {
        .voting-page {
            padding-top: 340px;
        }
    }

    .voting-page {
        background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
        padding-bottom: 3rem;
        clear: both;
        position: relative;
        z-index: 1;
        min-height: 100vh;
        padding-top: 90px;
    }
    
    .voting-page .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    @media (max-width: 576px) {
        .voting-page .container {
            padding-left: 12px;
            padding-right: 12px;
        }
    }

    .voting-title {
        font-size: 2.8rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #ff6b9d 0%, #ff1744 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        margin-top: 0;
    }

    @media (max-width: 768px) {
        .voting-title {
            font-size: 2.2rem;
            margin-bottom: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .voting-title {
            font-size: 1.8rem;
            margin-bottom: 0.7rem;
        }
    }

    @media (max-width: 420px) {
        .voting-title {
            font-size: 1.6rem;
            margin-bottom: 0.6rem;
        }
    }

    .voting-subtitle {
        font-size: 1.1rem;
        margin-bottom: 0 !important;
        margin-top: 0.5rem !important;
        color: #ccc;
        line-height: 1.6;
        max-width: 600px;
    }

    @media (max-width: 768px) {
        .voting-subtitle {
            font-size: 1rem;
        }
    }

    @media (max-width: 576px) {
        .voting-subtitle {
            font-size: 0.95rem;
            line-height: 1.5;
        }
    }

    .voting-header {
        margin-bottom: 4rem;
        padding: 4rem 0 4rem 0;
    }

    @media (max-width: 768px) {
        .voting-header {
            margin-bottom: 3.5rem;
            padding: 3.5rem 0 3.5rem 0;
        }
    }

    @media (max-width: 576px) {
        .voting-header {
            margin-bottom: 3rem;
            padding: 5rem 0 4rem 0;
        }
    }

    @media (max-width: 420px) {
        .voting-header {
            margin-bottom: 2.5rem;
            padding: 5rem 0 4rem 0;
        }
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 2.5rem;
        margin-top: 1rem;
        position: relative;
        padding-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
        }
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #ff6b9d 0%, #ffd700 100%);
        border-radius: 2px;
    }

    /* Top 3 Cards - Medalões Layout */
    .top-3-section {
        margin-bottom: 3rem;
        margin-top: 1rem;
    }

    .top-3-section .section-title {
        margin-bottom: 3rem;
    }

    @media (max-width: 576px) {
        .top-3-section {
            margin-bottom: 2rem;
            margin-top: 0.5rem;
        }

        .top-3-section .section-title {
            margin-bottom: 2rem;
        }
    }

    .layout-medaloes {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        grid-template-rows: auto auto;
        justify-items: center;
        align-items: flex-end;
        gap: 2rem;
        margin-bottom: 2rem;
        padding: 2rem 1rem;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 576px) {
        .layout-medaloes {
            padding: 1.5rem 0.5rem;
            margin-bottom: 5rem;
        }
    }

    .top-3-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 200px;
        animation: fadeInUp 0.6s ease-out backwards;
        position: relative;
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.08) 0%, rgba(255, 23, 68, 0.04) 100%);
        border: 2px solid rgba(255, 107, 157, 0.2);
        border-radius: 16px;
        padding: 12px;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        aspect-ratio: 1 / 1.15;
    }

    .top-3-card:hover {
        border-color: rgba(255, 107, 157, 0.4);
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.12) 0%, rgba(255, 23, 68, 0.06) 100%);
    }

    /* Posicionamento no grid */
    .top-3-card.position-2 {
        grid-column: 1;
        grid-row: 2;
        animation-delay: 0s;
    }

    .top-3-card.position-1 {
        grid-column: 2;
        grid-row: 1 / 3;
        animation-delay: 0.15s;
        transform: translateY(0) scale(1.18);
        justify-self: center;
    }

    .top-3-card.position-3 {
        grid-column: 3;
        grid-row: 2;
        animation-delay: 0.1s;
    }

    .top-3-card.position-1:hover {
        transform: translateY(-20px) scale(1.28);
    }

    .top-3-card.position-2:hover,
    .top-3-card.position-3:hover {
        transform: translateY(-15px);
    }

    .circle-wrapper {
        position: relative;
        margin-bottom: 0.8rem;
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: 0.3rem;
        flex-shrink: 0;
    }

    .top-3-image {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 5px solid;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        background: linear-gradient(135deg, rgba(100, 50, 80, 0.3), rgba(50, 100, 120, 0.3));
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
        object-fit: cover;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    }

    .top-3-card.position-1 .top-3-image {
        width: 200px;
        height: 200px;
        border-color: #fbbf24;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6), 0 0 50px rgba(251, 191, 36, 0.6);
    }

    .top-3-card.position-2 .top-3-image {
        border-color: #e5e7eb;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(229, 231, 235, 0.4);
    }

    .top-3-card.position-3 .top-3-image {
        border-color: #d97706;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(217, 119, 6, 0.4);
    }

    .top-3-position-badge {
        position: absolute;
        top: -16px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1.8rem;
        z-index: 10;
        border: 3px solid #fff;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.7);
        transition: all 0.3s ease;
    }

    .top-3-card.position-1 .top-3-position-badge {
        width: 80px;
        height: 80px;
        font-size: 2.2rem;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        box-shadow: 0 0 30px rgba(251, 191, 36, 0.7), 0 6px 16px rgba(0, 0, 0, 0.7);
        color: #000;
        border-color: #fff;
        top: -20px;
    }

    .top-3-card.position-2 .top-3-position-badge {
        background: linear-gradient(135deg, #e5e7eb, #d1d5db);
        color: #333;
        border-color: #fff;
    }

    .top-3-card.position-3 .top-3-position-badge {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #fff;
        border-color: #fff;
    }

    .top-3-info {
        text-align: center;
        width: 100%;
        padding: 0 8px;
    }

    .top-3-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
        word-break: break-word;
        line-height: 1.2;
    }

    .top-3-stats {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .top-3-votes {
        font-size: 0.95rem;
        color: #bbb;
        font-weight: 500;
    }

    .top-3-percentage {
        font-size: 1.2rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #ff6b9d 0%, #ff1744 100%);
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.9rem;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Candidate Cards */
    .candidate-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 107, 157, 0.02) 100%);
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        cursor: pointer;
        position: relative;
        height: 100%;
    }

    .candidate-card:hover {
        border-color: rgba(255, 107, 157, 0.4);
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.12) 0%, rgba(255, 107, 157, 0.05) 100%);
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(255, 107, 157, 0.2);
    }

    .candidate-card.voted {
        border-color: #4CAF50;
        box-shadow: 0 0 25px rgba(76, 175, 80, 0.35);
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.03) 100%);
    }

    .candidate-image-wrapper {
        position: relative;
        width: 100%;
        height: 280px;
        overflow: hidden;
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        border-radius: 16px 16px 0 0;
    }

    .candidate-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    }

    .candidate-card:hover .candidate-image {
        transform: scale(1.08);
    }

    .vote-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: white;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: none;
        z-index: 5;
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
    }

    .candidate-card.voted .vote-badge {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .candidate-info {
        padding: 18px;
        background: linear-gradient(135deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.05) 100%);
    }

    .candidate-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .candidate-votes {
        font-size: 0.88rem;
        color: #aaa;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .vote-button {
        width: auto;
        min-width: 100%;
        padding: 14px 18px;
        background: linear-gradient(135deg, #ff6b9d 0%, #ff1744 100%);
        color: white;
        border: 2px solid #ff1744 !important;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        font-size: 1.15rem;
        box-shadow: 0 5px 15px rgba(255, 23, 68, 0.3);
        display: block;
        margin-top: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    
    .vote-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transition: left 0.3s ease;
    }
    
    .vote-button:hover::before {
        left: 100%;
    }

    .vote-button:hover {
        background: linear-gradient(135deg, #ff5287 0%, #dd1744 100%);
        transform: scale(1.08) translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 23, 68, 0.5);
        border-color: #ff1744 !important;
    }
    
    .vote-button:disabled {
        opacity: 0.8;
        cursor: not-allowed;
        transform: none;
    }
    
    .vote-button .spinner-border {
        width: 18px;
        height: 18px;
        border-width: 2px;
    }

    .candidate-card.voted .vote-button {
        background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }

    .candidate-card.voted .vote-button:hover {
        background: linear-gradient(135deg, #66BB6A 0%, #2E7D32 100%);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    }

    /* Modal Styles */
    .voting-benefits {
        padding: 1.5rem 0;
        margin: 1.5rem 0;
    }

    .benefit-item {
        margin-bottom: 1rem !important;
    }

    .benefit-item strong {
        font-size: 1rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .benefit-item p {
        font-size: 0.9rem;
    }

    @media (max-width: 576px) {
        .voting-benefits {
            padding: 1rem 0;
            margin: 1rem 0;
        }

        .benefit-item {
            margin-bottom: 0.75rem !important;
        }

        .benefit-item strong {
            font-size: 0.95rem;
        }

        .benefit-item p {
            font-size: 0.85rem;
        }
    }

    .voting-modal {
        background: linear-gradient(135deg, rgba(15, 15, 15, 0.98) 0%, rgba(26, 26, 26, 0.98) 100%);
        border: 2px solid rgba(255, 107, 157, 0.2);
        border-radius: 20px;
        color: #fff;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
    }

    .voting-modal .modal-header {
        border-bottom: 1px solid rgba(255, 107, 157, 0.1);
        background: rgba(0, 0, 0, 0.3);
        padding: 25px;
    }

    .voting-modal .modal-footer {
        border-top: 1px solid rgba(255, 107, 157, 0.1);
        background: rgba(0, 0, 0, 0.2);
        padding: 20px 25px;
    }

    .voting-benefits {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.1) 0%, rgba(76, 175, 80, 0.05) 100%);
        padding: 22px;
        border-radius: 12px;
        border-left: 4px solid #4CAF50;
    }

    .btn-purchase {
        background: linear-gradient(135deg, #ff6b9d 0%, #ff1744 100%);
        border-color: #ff1744;
        font-weight: 700;
        padding: 12px 24px;
        box-shadow: 0 5px 15px rgba(255, 23, 68, 0.3);
        transition: all 0.3s ease;
    }

    .btn-purchase:hover {
        background: linear-gradient(135deg, #ff5287 0%, #dd1744 100%);
        border-color: #ff1744;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 23, 68, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .layout-medaloes {
            gap: 1.5rem;
        }

        .top-3-card.position-1 {
            transform: translateY(-35px) scale(1.12);
        }

        .top-3-card.position-1:hover {
            transform: translateY(-55px) scale(1.20);
        }

        .top-3-image {
            width: 140px;
            height: 140px;
        }

        .top-3-card.position-1 .top-3-image {
            width: 170px;
            height: 170px;
        }

        .top-3-position-badge {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }

        .top-3-card.position-1 .top-3-position-badge {
            width: 90px;
            height: 90px;
            font-size: 2.2rem;
        }
    }

    @media (max-width: 768px) {
        .layout-medaloes {
            gap: 1rem;
            padding: 1.5rem 0.5rem;
        }

        .top-3-card {
            max-width: 150px;
        }

        .top-3-card.position-1 {
            transform: translateY(-30px) scale(1.10);
        }

        .top-3-card.position-1:hover {
            transform: translateY(-45px) scale(1.15);
        }

        .top-3-image {
            width: 130px;
            height: 130px;
        }

        .top-3-card.position-1 .top-3-image {
            width: 160px;
            height: 160px;
        }

        .top-3-position-badge {
            width: 65px;
            height: 65px;
            font-size: 1.8rem;
            top: -20px;
            right: -20px;
        }

        .top-3-card.position-1 .top-3-position-badge {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .top-3-name {
            font-size: 1.1rem;
        }

        .top-3-votes {
            font-size: 0.85rem;
        }

        .top-3-percentage {
            font-size: 1rem;
            padding: 6px 12px;
        }
    }

    @media (max-width: 576px) {
        .layout-medaloes {
            display: block !important;
            position: relative;
            width: 100%;
            max-width: 95%;
            height: auto;
            min-height: 400px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 4rem;
            background: transparent;
            border: none;
            border-radius: 18px;
            padding: 2.5rem 0.5rem 2rem 0.5rem;
        }

        .top-3-card {
            position: absolute !important;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 8px 8px 8px !important;
            aspect-ratio: 1 / 1.5 !important;
            width: 160px !important;
            height: auto !important;
            justify-content: flex-start;
            gap: 4px;
        }

        .top-3-card.position-2 {
            left: -20px;
            top: 0;
        }

        .top-3-card.position-1 {
            right: -30px;
            left: auto;
            top: 95px;
            transform: none;
            width: 165px !important;
            aspect-ratio: 1 / 1.8 !important;
            z-index: 3;
        }

        .top-3-card.position-1:hover {
            transform: scale(1.05);
        }

        .top-3-card.position-3 {
            left: -20px;
            top: 290px;
        }

        .circle-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2px;
            position: relative;
            width: 100%;
            gap: 0px;
        }

        .top-3-position-badge {
            width: 12px;
            height: 12px;
            font-size: 0.6rem;
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 1px;
            flex-shrink: 0;
            z-index: 10;
        }

        .top-3-card.position-1 .top-3-position-badge {
            width: 16px;
            height: 16px;
            font-size: 0.85rem;
            top: -10px;
        }

        .top-3-image {
            width: 36px;
            height: 36px;
            margin-bottom: 0px;
            margin-top: 8px;
            flex-shrink: 0;
        }

        .top-3-card.position-2 .top-3-image,
        .top-3-card.position-3 .top-3-image {
            width: 106px;
            height: 106px;
        }

        .top-3-card.position-1 .top-3-image {
            width: 42px;
            height: 42px;
            margin-top: 10px;
        }

        .top-3-info {
            margin-bottom: 0;
            margin-top: 4px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .top-3-name {
            font-size: 0.75rem;
            margin-bottom: 2px;
            line-height: 1.1;
            word-break: break-word;
        }

        .top-3-votes {
            font-size: 0.65rem;
        }

        .top-3-percentage {
            font-size: 0.65rem;
            padding: 2px 4px;
        }

        .top-3-stats {
            display: flex;
            gap: 3px;
            font-size: 0.65rem;
            justify-content: center;
            margin-bottom: 2px;
        }

        .top-3-card .btn {
            font-size: 0.55rem !important;
            padding: 0.25rem 0.35rem !important;
            margin: 0 !important;
            height: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .top-3-card .btn i {
            font-size: 0.5rem;
            margin: 0 !important;
        }
    }

    @media (max-width: 420px) {
        .layout-medaloes {
            display: block !important;
            position: relative;
            width: 100%;
            max-width: 85%;
            height: auto;
            min-height: 350px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 8rem;
            background: transparent;
            border: none;
            border-radius: 14px;
            padding: 1.8rem 0.3rem 2rem 0.3rem;
        }

        .top-3-card {
            position: absolute !important;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 6px 6px 6px !important;
            aspect-ratio: 1 / 1.5 !important;
            width: 140px !important;
            height: auto !important;
            justify-content: flex-start;
            gap: 3px;
        }

        .top-3-card.position-2 {
            left: -15px;
            top: 0;
        }

        .top-3-card.position-1 {
            right: -25px;
            left: auto;
            top: 80px;
            transform: none;
            width: 150px !important;
            aspect-ratio: 1 / 1.8 !important;
            z-index: 3;
        }

        .top-3-card.position-1:hover {
            transform: scale(1.04);
        }

        .top-3-card.position-3 {
            left: -15px;
            top: 250px;
        }

        .circle-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 0px;
            position: relative;
            width: 100%;
            gap: 0px;
        }

        .top-3-position-badge {
            width: 8px;
            height: 8px;
            font-size: 0.5rem;
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 1px;
            flex-shrink: 0;
            z-index: 10;
        }

        .top-3-card.position-1 .top-3-position-badge {
            width: 12px;
            height: 12px;
            font-size: 0.75rem;
            top: -8px;
        }

        .top-3-image {
            width: 32px;
            height: 32px;
            margin-bottom: 0px;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .top-3-card.position-2 .top-3-image,
        .top-3-card.position-3 .top-3-image {
            width: 96px;
            height: 96px;
        }

        .top-3-card.position-1 .top-3-image {
            width: 38px;
            height: 38px;
            margin-top: 8px;
        }

        .top-3-info {
            margin-bottom: 0;
            margin-top: 3px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .top-3-name {
            font-size: 0.6rem;
            margin-bottom: 2px;
            line-height: 1;
            word-break: break-word;
        }

        .top-3-votes {
            font-size: 0.5rem;
        }

        .top-3-percentage {
            font-size: 0.5rem;
            padding: 1px 3px;
        }

        .top-3-stats {
            display: flex;
            gap: 2px;
            font-size: 0.5rem;
            justify-content: center;
            margin-bottom: 2px;
        }

        .top-3-card .btn {
            font-size: 0.5rem !important;
            padding: 0.2rem 0.3rem !important;
            margin: 0 !important;
            height: 16px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .top-3-card .btn i {
            font-size: 0.45rem;
            margin: 0 !important;
        }
    }

    @media (max-width: 768px) {
        .layout-medaloes {
            gap: 1rem;
            padding: 1.5rem 0.5rem;
            grid-template-columns: 1fr 1.1fr 1fr;
            grid-template-rows: auto auto;
        }

        .top-3-card {
            max-width: 150px;
            padding: 10px;
            aspect-ratio: 1 / 1.1;
        }

        .top-3-card.position-1 {
            transform: translateY(0) scale(1.10);
        }

        .top-3-card.position-1:hover {
            transform: translateY(-15px) scale(1.15);
        }

        .top-3-image {
            width: 130px;
            height: 130px;
        }

        .top-3-card.position-1 .top-3-image {
            width: 160px;
            height: 160px;
        }

        .top-3-position-badge {
            width: 50px;
            height: 50px;
            font-size: 1.4rem;
            top: -14px;
            border-width: 2.5px;
        }

        .top-3-card.position-1 .top-3-position-badge {
            width: 65px;
            height: 65px;
            font-size: 1.7rem;
            top: -17px;
        }

        .top-3-name {
            font-size: 1.1rem;
        }

        .top-3-votes {
            font-size: 0.85rem;
        }

        .top-3-percentage {
            font-size: 0.85rem;
            padding: 5px 10px;
        }

        .circle-wrapper {
            margin-bottom: 0.7rem;
            margin-top: 0.1rem;
        }

        .voting-title {
            font-size: 1.8rem;
        }

        .voting-subtitle {
            font-size: 1rem;
        }

        .section-title {
            font-size: 1.3rem;
        }

        .candidate-image-wrapper {
            height: 200px;
        }

        .row.g-3 > .col-6 {
            padding-right: 6px;
            padding-left: 6px;
        }
    }

    @media (max-width: 576px) {
        .voting-page {
            padding-top: 60px;
        }
        
        .voting-title {
            font-size: 1.5rem;
            margin-bottom: 0.3rem;
        }

        .voting-subtitle {
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.6rem;
        }
        
        .section-title:after {
            width: 50px;
            height: 2px;
        }

        .top-3-image {
            height: 150px;
        }

        .top-3-position-badge {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .top-3-name {
            font-size: 1rem;
        }

        .candidate-image-wrapper {
            height: 180px;
        }

        .candidate-info {
            padding: 12px;
        }

        .candidate-name {
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .candidate-votes {
            font-size: 0.75rem;
            margin-bottom: 10px;
        }

        .btn.btn-outline-danger {
            font-size: inherit !important;
            padding: inherit !important;
        }
        
        .vote-button {
            padding: 20px 24px 52px 24px !important;
        }
        
        .row.g-3 > .col-6 {
            padding-left: 4px;
            padding-right: 4px;
        }
    }

    /* Loading state */
    .spinner-border {
        width: 50px;
        height: 50px;
    }

    /* Success message */
    .toast-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Suggestion Section Styles */
    .suggestions-section {
        margin-top: 7rem;
        padding: 2rem 0;
        width: 100%;
    }

    .suggest-section {
        background: linear-gradient(135deg, rgba(255, 107, 157, 0.08) 0%, rgba(255, 23, 68, 0.04) 100%);
        border: 2px solid rgba(255, 107, 157, 0.15);
        border-radius: 20px;
        padding: 2.5rem;
        max-width: 600px;
        width: 100%;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    }

    @media (max-width: 768px) {
        .suggest-section {
            padding: 1.5rem;
            border-radius: 16px;
        }
    }

    @media (max-width: 576px) {
        .suggest-section {
            padding: 1.2rem;
            border-radius: 12px;
        }

        .suggestions-section {
            margin-top: 6rem;
            padding: 1rem 0;
        }
    }

    .suggest-section:hover {
        border-color: rgba(255, 107, 157, 0.3);
        box-shadow: 0 15px 50px rgba(255, 107, 157, 0.2);
    }

    .suggest-description {
        color: #bbb;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .suggestion-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        width: 100%;
    }

    .form-group label {
        font-weight: 700;
        color: #fff;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    @media (max-width: 576px) {
        .form-group label {
            font-size: 0.95rem;
        }
    }

    .required {
        color: #ff6b9d;
        font-weight: 800;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        font-family: inherit;
        box-sizing: border-box;
    }

    @media (max-width: 576px) {
        .form-input {
            padding: 10px 12px;
            font-size: 0.9rem;
            border-radius: 8px;
        }
    }

    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .form-input:focus {
        outline: none;
        border-color: #ff6b9d;
        background: rgba(255, 107, 157, 0.08);
        box-shadow: 0 0 0 4px rgba(255, 107, 157, 0.15);
    }

    .form-input:hover:not(:focus) {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    .form-hint {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.5);
        display: block;
    }

    .suggest-button {
        width: 100%;
        padding: 13px 24px;
        background: linear-gradient(135deg, #ff6b9d 0%, #ff1744 100%);
        border: none;
        color: #fff;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        box-shadow: 0 8px 20px rgba(255, 23, 68, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        margin-top: 0.5rem;
        box-sizing: border-box;
    }

    @media (max-width: 576px) {
        .suggest-button {
            padding: 11px 18px;
            font-size: 0.9rem;
            border-radius: 8px;
        }
    }

    .suggest-button:hover {
        background: linear-gradient(135deg, #ff5287 0%, #dd1744 100%);
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 23, 68, 0.4);
    }

    .suggest-button:active {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(255, 23, 68, 0.3);
    }

    .suggest-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .suggestion-message {
        margin-top: 1rem;
    }

    .suggestion-message .alert {
        margin: 0;
        border-radius: 10px;
        padding: 14px 16px;
        border: 1px solid;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-weight: 500;
        animation: slideInUp 0.3s ease-out;
    }

    .alert-success {
        background-color: rgba(76, 175, 80, 0.15);
        border-color: rgba(76, 175, 80, 0.4);
        color: #90EE90;
    }

    .alert-danger {
        background-color: rgba(255, 23, 68, 0.15);
        border-color: rgba(255, 23, 68, 0.4);
        color: #ff6b9d;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .suggestions-section {
            margin-top: 3rem;
            padding: 1.5rem 0;
        }

        .suggest-section {
            padding: 1.8rem 1.5rem;
            border-radius: 16px;
        }

        .suggest-description {
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
            margin-bottom: 1.2rem;
        }

        .form-group label {
            font-size: 0.95rem;
        }

        .form-input {
            padding: 11px 14px;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .form-hint {
            font-size: 0.8rem;
        }

        .suggest-button {
            padding: 12px 20px;
            font-size: 0.9rem;
            border-radius: 8px;
        }

        .suggestion-message .alert {
            padding: 12px 14px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .suggestions-section {
            margin-top: 2rem;
            padding: 1rem 0;
        }

        .suggest-section {
            padding: 1.5rem 1.2rem;
            border-radius: 14px;
            margin: 0;
            border-width: 1px;
        }

        .suggest-description {
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .form-group {
            gap: 0.5rem;
        }

        .form-group label {
            font-size: 0.9rem;
        }

        .form-input {
            padding: 10px 12px;
            font-size: 0.85rem;
        }

        .form-hint {
            font-size: 0.75rem;
        }

        .suggest-button {
            padding: 11px 16px;
            font-size: 0.85rem;
            gap: 0.4rem;
        }

        .suggestion-message .alert {
            padding: 10px 12px;
            font-size: 0.85rem;
            gap: 0.6rem;
        }
    }

    /* Estilos do Modal de Limite de Votos */
    .voting-limit-modal {
        display: none !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 99999 !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .voting-limit-modal::before {
        content: '' !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0, 0, 0, 0.5) !important;
        z-index: 99998 !important;
        display: none !important;
    }

    .voting-limit-modal.active {
        display: flex !important;
    }

    .voting-limit-modal.active::before {
        display: block !important;
    }

    .voting-limit-modal-overlay {
        display: none !important;
    }

    .voting-limit-modal-content {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 100000 !important;
        background: white !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
        max-width: 400px !important;
        width: 90% !important;
        overflow: hidden !important;
        animation: slideUp 0.3s ease-out !important;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .voting-limit-modal-header {
        background: linear-gradient(135deg, #FF6B9D 0%, #FF1744 100%);
        color: white;
        padding: 20px;
        text-align: center;
        font-weight: 700;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .voting-limit-modal-header i {
        font-size: 1.5rem;
    }

    .voting-limit-modal-body {
        padding: 24px 20px;
        text-align: center;
        color: #333;
    }

    .voting-limit-modal-body p {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .voting-limit-modal-footer {
        padding: 16px 20px;
        border-top: 1px solid #f0f0f0;
        text-align: center;
    }

    .voting-limit-modal-button {
        background: linear-gradient(135deg, #FF6B9D 0%, #FF1744 100%);
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .voting-limit-modal-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 23, 68, 0.3);
    }

    .voting-limit-modal-button:active {
        transform: translateY(0);
    }

    @media (max-width: 576px) {
        .voting-limit-modal-content {
            max-width: 95%;
        }

        .voting-limit-modal-header {
            padding: 16px;
            font-size: 1rem;
        }

        .voting-limit-modal-body {
            padding: 20px 16px;
            font-size: 0.9rem;
        }

        .voting-limit-modal-footer {
            padding: 12px 16px;
        }

        .voting-limit-modal-button {
            padding: 8px 24px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

<!-- Scripts -->
@push('after-scripts')
<script>
    const votingAPI = {
        baseURL: '/api/v1',

        async getTop3() {
            try {
                const response = await fetch(`${this.baseURL}/voting/top-3`);
                const data = await response.json();
                return data.data || [];
            } catch (error) {
                console.error('Erro ao carregar top 3:', error);
                return [];
            }
        },

        async getAllCandidates() {
            try {
                const response = await fetch(`${this.baseURL}/voting/all-candidates`);
                const data = await response.json();
                return data.data || [];
            } catch (error) {
                console.error('Erro ao carregar candidatos:', error);
                return [];
            }
        },

        async vote(contentSlug) {
            try {
                console.log('Votando em:', contentSlug);
                
                const response = await fetch(`${this.baseURL}/voting/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content_slug: contentSlug })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Erro na resposta:', errorData);
                    return { 
                        success: false, 
                        message: errorData.message || `Erro HTTP ${response.status}`,
                        status: response.status
                    };
                }
                
                const data = await response.json();
                console.log('Resposta do voto:', data);
                return data;
            } catch (error) {
                console.error('Erro ao votar:', error);
                return { success: false, message: 'Erro ao registrar voto' };
            }
        },

        async getUserVote(weekId) {
            try {
                const response = await fetch(`${this.baseURL}/voting/user-vote/${weekId}`);
                const data = await response.json();
                return data.data;
            } catch (error) {
                console.error('Erro ao carregar voto do usuário:', error);
                return null;
            }
        }
    };

    // Initialize page
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('Página de votação carregada');
        console.log('window.votingTranslations:', window.votingTranslations);
        
        // Verificar se o modal de limite existe
        const limitModal = document.getElementById('votingLimitModal');
        if (limitModal) {
            console.log('Modal de limite encontrado:', limitModal.className);
            
            // Adicionar event listener ao botão
            const closeBtn = document.getElementById('votingLimitButton');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', closeVotingLimitModal);
                console.log('Event listener adicionado ao botão');
            }
            
            // Fechar ao clicar no overlay (::before)
            limitModal.addEventListener('click', function(e) {
                if (e.target === limitModal) {
                    console.log('Clique no overlay detectado');
                    closeVotingLimitModal();
                }
            });
        } else {
            console.error('Modal de limite NÃO encontrado!');
        }
        
        await loadTop3();
    });

    async function loadTop3() {
        const container = document.getElementById('top3Container');
        if (!container) {
            console.error('Container top3Container não encontrado!');
            return;
        }
        
        console.log('Carregando Top 3...');
        const top3Data = await votingAPI.getTop3();
        console.log('Dados Top 3 recebidos:', top3Data);

        if (!top3Data || top3Data.length === 0) {
            console.log('Nenhum dado recebido');
            container.innerHTML = '<div class="col-12 text-center text-muted py-4">' + window.votingTranslations.noVotesYet + '</div>';
            return;
        }

        const html = top3Data.map((item, idx) => {
            console.log(`Item ${idx}:`, item);
            return `
            <div class="top-3-card position-${item.position}">
                <div class="circle-wrapper">
                    <div class="top-3-position-badge position-${item.position}">${item.position}</div>
                    ${item.image ? `<img src="${item.image}" alt="${item.name}" class="top-3-image" onerror="this.src='/images/placeholder.jpg'">` : '<div class="top-3-image" style="background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);"></div>'}
                </div>
                <div class="top-3-info">
                    <div class="top-3-name">${item.name}</div>
                    <div class="top-3-stats">
                        <span class="top-3-votes">${item.total_votes} ${window.votingTranslations.votes}</span>
                        <span class="top-3-percentage">${item.percentage}%</span>
                    </div>
                    <button class="vote-button btn btn-sm btn-outline-danger mt-2" onclick="handleVote('${item.id}', this)" style="transition: all 0.3s ease; border-radius: 12px; font-weight: 700; letter-spacing: 0.5px;">
                        <i class="ph ph-heart me-1"></i> ${window.votingTranslations.voting}
                    </button>
                </div>
            </div>
        `;
        }).join('');

        console.log('HTML gerado (primeiros 500 chars):', html.substring(0, 500));
        console.log('Container innerHTML antes:', container.innerHTML.substring(0, 200));
        container.innerHTML = html;
        console.log('Container innerHTML depois:', container.innerHTML.substring(0, 200));
    }

    async function loadCandidates() {
        const container = document.getElementById('candidatesContainer');
        if (!container) return;
        
        try {
            const candidates = await votingAPI.getAllCandidates();
            console.log('Candidatos carregados:', candidates);

            if (!candidates || candidates.length === 0) {
                container.innerHTML = '<div class="col-12 text-center text-muted py-4">Nenhum candidato disponível</div>';
                return;
            }

            const html = candidates.map(candidate => `
                <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                    <div class="candidate-card" data-candidate-id="${candidate.id}">
                        <div class="vote-badge">
                            <i class="ph ph-check-circle"></i> Votado
                        </div>
                        <div class="candidate-image-wrapper">
                            ${candidate.image ? `<img src="${candidate.image}" alt="${candidate.name}" class="candidate-image" onerror="this.src='/images/placeholder.jpg'">` : '<div class="candidate-image" style="background: #333;"></div>'}
                        </div>
                        <div class="candidate-info">
                            <div class="candidate-name">${candidate.name}</div>
                            <div class="candidate-votes">${candidate.votes} ${window.votingTranslations.votes}</div>
                            <button class="vote-button" onclick="handleVote('${candidate.id}')" data-candidate-id="${candidate.id}">
                                <i class="ph ph-heart-half"></i> Votar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = html;
        } catch (error) {
            console.error('Erro ao carregar candidatos:', error);
            container.innerHTML = '<div class="col-12 text-center text-danger py-4">Erro ao carregar candidatos</div>';
        }
    }

    async function handleVote(contentSlug, buttonElement) {
        console.log('Iniciando voto para:', contentSlug);
        
        // Se não passou buttonElement, procura o botão mais próximo
        if (!buttonElement) {
            buttonElement = event?.target?.closest('.vote-button') || event?.target?.closest('button');
        }
        
        if (buttonElement) {
            buttonElement.disabled = true;
            buttonElement.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Votando...';
        }
        
        const result = await votingAPI.vote(contentSlug);
        console.log('Resultado do voto:', result);

        if (result.success) {
            showToast(window.votingTranslations.votingSuccess, 'success');
            
            // Marca a opção como votada (procura por card com data-candidate-id)
            const card = document.querySelector(`[data-candidate-id="${contentSlug}"]`);
            if (card) {
                card.classList.add('voted');
            }
            
            console.log('Voto bem-sucedido, recarregando em 1s...');
            setTimeout(async () => {
                console.log('Iniciando reload de loadTop3...');
                await loadTop3();
                console.log('loadTop3 concluído');
                await loadCandidates();
                console.log('loadCandidates concluído');
            }, 1000);
        } else {
            // Verifica se é erro de limite (429)
            if (result.status === 429) {
                showVotingLimitModal();
            } else {
                showToast(result.message || window.votingTranslations.votingError, 'error');
            }
            
            if (buttonElement) {
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="ph ph-heart me-1"></i> ' + window.votingTranslations.voting;
            }
        }
    }

    let votingLimitModalTimeout;

    function showVotingLimitModal() {
        const modal = document.getElementById('votingLimitModal');
        const messageEl = document.getElementById('votingLimitMessage');
        
        console.log('Abrindo modal de limite');
        console.log('Modal element:', modal);
        
        // Limpar qualquer timeout anterior
        if (votingLimitModalTimeout) {
            clearTimeout(votingLimitModalTimeout);
        }
        
        // Atualizar mensagem com tradução
        messageEl.textContent = window.votingTranslations.votingLimit;
        
        // Mostrar modal
        modal.classList.add('active');
        console.log('Modal classes após add active:', modal.className);
        
        // Fechar automaticamente em 4 segundos
        votingLimitModalTimeout = setTimeout(() => {
            console.log('Fechando modal automaticamente');
            closeVotingLimitModal();
        }, 4000);
    }

    function closeVotingLimitModal() {
        console.log('closeVotingLimitModal chamado!');
        const modal = document.getElementById('votingLimitModal');
        console.log('Modal encontrado:', modal);
        
        // Limpar timeout se existir
        if (votingLimitModalTimeout) {
            clearTimeout(votingLimitModalTimeout);
        }
        
        if (modal) {
            console.log('Classes antes:', modal.className);
            modal.classList.remove('active');
            console.log('Classes depois:', modal.className);
            console.log('Classe active removida');
        }
    }
    
    // Tornar acessível globalmente
    window.closeVotingLimitModal = closeVotingLimitModal;


    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        toast.innerHTML = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Suggestion form handler
    document.getElementById('suggestionForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const button = form.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('suggestionMessage');
        const alertDiv = messageDiv.querySelector('.alert');

        // Get ranking ID from the page
        const rankingId = {{ $rankingId ?? 'null' }};
        if (!rankingId) {
            showToast('Erro: Ranking não encontrado', 'error');
            return;
        }

        const name = document.getElementById('suggestionName').value.trim();
        const link = document.getElementById('suggestionLink').value.trim();

        if (!name || !link) {
            showToast('Por favor, preencha todos os campos', 'warning');
            return;
        }

        button.disabled = true;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

        try {
            const response = await fetch('/api/v1/voting/suggest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ranking_id: rankingId,
                    sugestion_name: name,
                    sugestion_link: link
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                form.reset();
                messageDiv.style.display = 'block';
                alertDiv.className = 'alert alert-success';
                alertDiv.innerHTML = '<i class="ph ph-check-circle"></i>' + data.message;
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 4000);
            } else {
                showToast(data.message || window.votingTranslations.suggestionError, 'error');
                messageDiv.style.display = 'block';
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = '<i class="ph ph-warning-circle"></i>' + (data.message || 'Erro desconhecido');
            }
        } catch (error) {
            console.error('Erro:', error);
            showToast(window.votingTranslations.suggestionError, 'error');
            messageDiv.style.display = 'block';
            alertDiv.className = 'alert alert-danger';
            alertDiv.innerHTML = '<i class="ph ph-warning-circle"></i>Erro de conexão';
        } finally {
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    });
</script>
@endpush
