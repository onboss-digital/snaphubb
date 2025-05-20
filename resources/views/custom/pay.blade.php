<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>SNAPHUBB Premium - Checkout</title>
    <link rel="icon" type="image/ico" href="{{ asset(setting('favicon')) }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Urbanist', sans-serif;
            background-color: #121212;
            color: white;
        }

        .animate-fade {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .sticky-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out;
        }

        .sticky-summary.show {
            transform: translateY(0);
        }

        .hidden-form {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }

        .show-form {
            max-height: 500px;
        }
    </style>
</head>

<body class="bg-[#121212] min-h-screen font-['Urbanist']">

    <form accept="" method="POST" action="{{ route('send-checkout') }}" id="payment-form">
        @csrf
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <!-- Progress Bar -->
            <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
                <div class="bg-[#E50914] h-2 rounded-full w-1/4 transition-all" id="progressBar"></div>
            </div>

            <!-- Header -->
            <header class="mb-8">
                <div class="flex flex-col md:flex-row items-center justify-between">

                    @php
                        $logo = GetSettingValue('dark_logo') ?? asset(setting('dark_logo'));
                    @endphp


                    <img class="img-fluid logo" src="{{ $logo }}" alt="streamit">

                    <!-- Language Selector -->
                    <div class="flex items-center space-x-4">
                        <div class="relative text-sm">
                            <select id="language-selector" name="language"
                                class="bg-[#1F1F1F] text-white rounded-md px-3 py-1.5 border border-gray-700 appearance-none pr-8 focus:outline-none focus:ring-1 focus:ring-[#E50914] hover:border-gray-500 transition-all">
                                <option value="pt-BR" selected>🇧🇷 Português</option>
                                <option value="en-US">🇺🇸 English</option>
                                <option value="es-ES">🇪🇸 Español</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 w-full h-32 md:h-48 rounded-xl overflow-hidden relative bg-gray-900">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#121212] via-transparent to-transparent"></div>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-white mt-6 text-center md:text-left">Iniciar sua
                    assinatura
                    Premium</h1>
                <p class="text-lg text-gray-300 mt-2 text-center md:text-left">Desbloqueie acesso exclusivo a todo nosso
                    conteúdo premium.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <!-- Currency Selector -->
                    <div class="bg-[#1F1F1F] rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-white mb-4">Selecione sua moeda</h2>

                        <div class="relative">
                            <select id="currency-selector" name="currency"
                                class="w-full bg-[#2D2D2D] text-white rounded-lg p-4 border border-gray-700 appearance-none pr-10 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all">
                                <option value="BRL">Real Brasileiro (BRL)</option>
                                <option value="USD">Dólar Americano (USD)</option>
                                <option value="EUR">Euro (EUR)</option>
                                <option value="GBP">Libra Esterlina (GBP)</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-white">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                    <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="bg-[#1F1F1F] rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-white mb-4">Seus benefícios Premium</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="benefits-container">
                            <!-- Benefits will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="bg-[#1F1F1F] rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-white mb-4">Método de pagamento</h2>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <input type="radio" id="payment-pix" name="payment_method" value="pix"
                                    class="peer sr-only" />
                                <label for="payment-pix"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-700 bg-[#2D2D2D] cursor-pointer transition-all hover:bg-gray-800 peer-checked:border-[#E50914] peer-checked:bg-[#2D2D2D] h-24">
                                    <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span class="text-sm font-medium text-white">PIX</span>
                                </label>
                            </div>

                            <div class="relative">
                                <input type="radio" id="payment-card" name="payment_method" value="credit_card"
                                    class="peer sr-only" checked />
                                <label for="payment-card"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border border-gray-700 bg-[#2D2D2D] cursor-pointer transition-all hover:bg-gray-800 peer-checked:border-[#E50914] peer-checked:bg-[#2D2D2D] h-24">
                                    <svg class="w-8 h-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    <span class="text-sm font-medium text-white">Cartão</span>
                                </label>
                            </div>

                        </div>

                        <!-- Card payment form - shown conditionally -->
                        <div id="card-payment-form" class="mt-6 hidden-form">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Número do
                                        cartão</label>
                                    <input name="card_number" type="text" id="card-number"
                                        placeholder="0000 0000 0000 0000"
                                        class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Data de
                                            expiração</label>
                                        <input name="card_expiry" type="text" id="card-expiry"
                                            placeholder="MM/AA"
                                            class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Código de
                                            segurança</label>
                                        <input name="card_cvv" type="text" id="card-cvv" placeholder="CVV"
                                            class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Nome no cartão</label>
                                    <input name="card_name" type="text" placeholder="Nome como aparece no cartão"
                                        class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">E-mail</label>
                                    <input name="email" type="text" placeholder="Seu email"
                                        class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Telefone</label>
                                    <input name="phone" type="text" placeholder="Numero de telefone"
                                        class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Bump -->
                    <div class="bg-[#1F1F1F] rounded-xl p-5 mb-6 border border-gray-700">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input name="order_bump" id="order-bump" type="checkbox"
                                    class="w-5 h-5 text-[#E50914] bg-[#2D2D2D] border-gray-600 rounded focus:ring-[#E50914] focus:ring-opacity-25 focus:ring-2 focus:border-[#E50914] cursor-pointer" />
                            </div>
                            <label for="order-bump" class="ml-3 cursor-pointer">
                                <div class="text-white text-base font-semibold flex items-center">
                                    <svg class="h-5 w-5 text-[#E50914] mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Acesso exclusivo a lives semanais
                                </div>
                                <p class="text-gray-400 text-sm mt-1">Participe de lives exclusivas com especialistas e
                                    convidados especiais toda semana.</p>
                                <p class="text-[#E50914] font-medium mt-2">+<span id="bump-price">R$9,99</span>/mês
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- Order Bump Unlock Animation -->
                    <div id="order-bump-unlock"
                        class="bg-[#1F1F1F] rounded-xl p-5 mb-6 border border-[#E50914] hidden animate-fade">
                        <div class="flex items-center justify-center text-center">
                            <div>
                                <div class="text-2xl mb-2">✨</div>
                                <p class="text-white font-semibold">Bônus desbloqueado: lives semanais</p>
                                <p class="text-gray-400 text-sm mt-1">Você agora tem acesso ao conteúdo exclusivo!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonials -->
                    <div class="bg-[#1F1F1F] rounded-xl p-6 mb-6">
                        <h2 class="text-xl font-semibold text-white mb-4">O que dizem nossos assinantes</h2>

                        <div class="space-y-4">
                            <div class="bg-[#2D2D2D] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-400">há 3 dias</span>
                                </div>
                                <p class="text-white text-sm">Assinante há 6 meses e não me arrependo! O conteúdo é
                                    incrível e sempre atualizado. As lives exclusivas são o melhor benefício, aprendo
                                    muito
                                    com os especialistas.</p>
                                <div class="mt-3 text-sm font-medium text-gray-400">
                                    Carlos M. - São Paulo
                                </div>
                            </div>

                            <div class="bg-[#2D2D2D] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-400">há 1 semana</span>
                                </div>
                                <p class="text-white text-sm">Vale cada centavo! Assinei o plano anual e economizei
                                    muito.
                                    A possibilidade de acessar todo o conteúdo offline é perfeita para quando estou
                                    viajando.</p>
                                <div class="mt-3 text-sm font-medium text-gray-400">
                                    Ana P. - Rio de Janeiro
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="md:col-span-1">
                    <div class="bg-[#1F1F1F] rounded-xl p-6 sticky top-6">
                        <h2 class="text-xl font-semibold text-white mb-4">Resumo do pedido</h2>

                        <!-- Timer -->
                        <div class="bg-[#2D2D2D] rounded-lg p-3 mb-4 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#E50914] mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-white">Oferta expira em <span id="countdown-timer"
                                    class="font-bold">14:22</span></span>
                        </div>

                        <!-- Plan selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Selecione seu plano</label>

                            <div class="relative">
                                <select id="plan-selector" name="plan"
                                    class="w-full bg-[#2D2D2D] text-white rounded-lg p-3 border border-gray-700 appearance-none pr-10 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all">
                                    <option value="monthly">Mensal</option>
                                    <option value="quarterly">Trimestral (10% off)</option>
                                    <option value="annual">Anual (25% off)</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
                                    </svg>
                                </div>
                            </div>

                            <p class="text-gray-400 text-sm mt-2">Plano flexível — cancele a qualquer momento</p>
                        </div>

                        <!-- Price anchor -->
                        <div class="mb-4 text-center">
                            <del class="text-gray-400 text-sm">R$89,90</del>
                            <span class="text-green-400 text-lg font-bold ml-2" id="current-price">R$58,99/mês</span>
                        </div>

                        <!-- Price breakdown -->
                        <div class="border-t border-b border-gray-700 py-4 mb-4 space-y-2">
                            <div class="flex justify-between text-gray-300">
                                <span>Assinatura Premium</span>
                                <span id="plan-price">R$58,99</span>
                            </div>

                            <div id="bump-price-row" class="flex justify-between text-gray-300 hidden">
                                <span>Acesso a lives semanais</span>
                                <span id="bump-price-summary">R$9,99</span>
                            </div>

                            <!-- Coupon area -->
                            <div class="mt-3 pt-3 border-t border-gray-700">
                                <div class="flex space-x-2">
                                    <input name="coupon_code" type="text" id="coupon-input"
                                        placeholder="Código de cupom"
                                        class="w-2/3 bg-[#2D2D2D] text-white rounded-lg p-2 text-sm border border-gray-700 focus:outline-none focus:ring-1 focus:ring-[#E50914] transition-all" />
                                    <button type="button" id="apply-coupon"
                                        class="w-1/3 bg-gray-700 hover:bg-gray-600 text-white text-sm py-2 px-2 rounded-lg transition-all">
                                        Aplicar
                                    </button>
                                </div>
                                <div id="coupon-message" class="text-xs mt-1 text-green-400 hidden">Cupom aplicado com
                                    sucesso!</div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-lg font-medium text-white">Total</span>
                            <span class="text-xl font-bold text-white" id="total-price">R$58,99</span>
                        </div>

                        <!-- Resumo Final com Descontos -->
                        <div id="priceSummary" class="mb-6 p-4 bg-gray-800 rounded-lg text-white">
                            <p class="text-base font-semibold mb-2">Resumo Final:</p>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Preço Original:</span>
                                <del class="text-gray-400">R$89,90</del>
                            </div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Desconto:</span>
                                <span class="text-green-400" id="discount-amount">-R$30,91</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold mt-2 pt-2 border-t border-gray-700">
                                <span>TOTAL A PAGAR:</span>
                                <span id="final-price">R$58,99</span>
                                <input type="hidden" id="input-final-price" name="final-price" value="" />
                            </div>
                        </div>

                        <!-- Limited spots -->
                        <div class="bg-[#2D2D2D] rounded-lg p-3 mb-4 text-center">
                            <span class="text-yellow-400 font-medium">Apenas <span id="spots-left">12</span> vagas
                                restantes!</span>
                        </div>

                        <!-- Live Activity Indicator -->
                        <div class="bg-[#2D2D2D] rounded-lg p-3 mb-6 text-center">
                            <span class="text-gray-400"><strong id="activityCounter">10</strong> pessoas estão
                                finalizando
                                agora!</span>
                        </div>

                        <!-- Verificação de Ambiente Seguro -->
                        <div id="seguranca"
                            class="w-full bg-gray-800 p-4 rounded-lg flex items-center gap-3 text-sm text-gray-300 animate-pulse mb-4 hidden">
                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2.003 5.884L10 2l7.997 3.884v4.632c0 5.522-3.936 10.74-7.997 11.484-4.061-.744-7.997-5.962-7.997-11.484V5.884z" />
                            </svg>
                            Verificando ambiente seguro...
                        </div>

                        <button id="checkout-button" type="button"
                            class="w-full bg-[#E50914] hover:bg-[#B8070F] text-white py-3 text-lg font-bold rounded-xl transition-all">
                            INICIAR SUBSCRIÇÃO PREMIUM
                        </button>

                        <!-- Trust badges -->
                        <div class="mt-4 flex flex-col items-center space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-green-500">✅</span>
                                <span class="text-sm text-gray-300">Garantia de 7 dias</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-green-500">🔒</span>
                                <span class="text-sm text-gray-300">SSL Seguro</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-green-500">🔁</span>
                                <span class="text-sm text-gray-300">Cancelamento fácil</span>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <div class="flex items-center justify-center space-x-2 mb-2">
                                <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span class="text-sm text-gray-300">100% anônimo e pagamento seguro</span>
                            </div>

                            <div class="flex justify-center space-x-3 text-xs text-gray-500">
                                <a href="#" class="hover:text-gray-300 transition-colors">Termos</a>
                                <a href="#" class="hover:text-gray-300 transition-colors">Privacidade</a>
                                <a href="#" class="hover:text-gray-300 transition-colors">Suporte</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Summary -->
            <div id="sticky-summary" class="sticky-summary bg-[#1F1F1F] border-t border-gray-700 md:hidden p-4">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-white font-medium" id="sticky-plan">Plano Mensal</div>
                            <div class="text-[#E50914]" id="sticky-price">R$58,99/mês</div>
                        </div>
                        <button type="button" id="sticky-checkout-button"
                            class="bg-[#E50914] hover:bg-[#B8070F] text-white py-2 px-4 text-sm font-bold rounded-lg transition-all">
                            ASSINAR AGORA
                        </button>
                    </div>
                </div>
            </div>

            <!-- Support Balloon - Agora oculto pois foi substituído pelo chat rápido -->
            <div id="support-balloon"
                class="fixed bottom-20 right-4 bg-[#1F1F1F] rounded-xl shadow-lg p-4 w-64 hidden">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-white">Suporte</h3>
                    <button type="button" id="close-support" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="text-sm text-gray-300 mb-3">
                    <p>Como podemos ajudar?</p>
                </div>
                <div class="space-y-2">
                    <div class="bg-[#2D2D2D] rounded-lg p-2 cursor-pointer hover:bg-gray-700 transition-colors">
                        <p class="text-sm text-white">Quanto tempo para ativar?</p>
                    </div>
                    <div class="bg-[#2D2D2D] rounded-lg p-2 cursor-pointer hover:bg-gray-700 transition-colors">
                        <p class="text-sm text-white">Como funciona o reembolso?</p>
                    </div>
                    <div class="bg-[#2D2D2D] rounded-lg p-2 cursor-pointer hover:bg-gray-700 transition-colors">
                        <p class="text-sm text-white">Posso cancelar a qualquer momento?</p>
                    </div>
                </div>
            </div>
        </div>


        <div id="test"></div>
    </form>
    <!-- Upsell Modal -->
    <div id="upsell-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#1F1F1F] rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <button id="close-upsell" class="absolute top-3 right-3 text-gray-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="text-center mb-4">
                    <div class="bg-[#E50914] rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Economize mais!</h3>
                    <p class="text-gray-300 mt-2">Assine o plano anual e ganhe 2 meses grátis!</p>
                </div>

                <div class="bg-[#2D2D2D] rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-300">Plano mensal (atual)</span>
                        <span class="text-white font-medium" id="upsell-monthly">R$58,99/mês</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium">Plano anual (oferta)</span>
                        <span class="text-[#E50914] font-bold" id="upsell-annual">R$44,24/mês</span>
                    </div>
                    <div class="mt-2 text-green-500 text-sm text-right" id="upsell-savings">
                        Economia de R$177,00/ano
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button id="upsell-reject"
                        class="py-3 text-white font-medium rounded-lg border border-gray-600 hover:bg-[#2D2D2D] transition-colors">
                        Manter mensal
                    </button>
                    <button id="upsell-accept"
                        class="py-3 bg-[#E50914] hover:bg-[#B8070F] text-white font-bold rounded-lg transition-colors">
                        Quero economizar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Downsell Modal -->
    <div id="downsell-modal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#1F1F1F] rounded-xl max-w-md w-full mx-4">
            <div class="p-6">
                <button id="close-downsell" class="absolute top-3 right-3 text-gray-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="text-center mb-4">
                    <div class="bg-[#E50914] rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.519 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.519-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Oferta especial</h3>
                    <p class="text-gray-300 mt-2">Experimente o trimestral com 15% de desconto!</p>
                </div>

                <div class="bg-[#2D2D2D] rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-300">Plano mensal (normal)</span>
                        <span class="text-white font-medium" id="downsell-monthly">R$58,99/mês</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-white font-medium">Plano trimestral (oferta)</span>
                        <span class="text-[#E50914] font-bold" id="downsell-quarterly">R$50,14/mês</span>
                    </div>
                    <div class="mt-2 text-green-500 text-sm text-right" id="downsell-savings">
                        Economia de R$26,55/trimestre
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button id="downsell-reject"
                        class="py-3 text-white font-medium rounded-lg border border-gray-600 hover:bg-[#2D2D2D] transition-colors">
                        Não, obrigado
                    </button>
                    <button id="downsell-accept"
                        class="py-3 bg-[#E50914] hover:bg-[#B8070F] text-white font-bold rounded-lg transition-colors">
                        Quero esta oferta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processing-modal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden">
        <div class="bg-[#1F1F1F] rounded-xl p-8 max-w-md w-full mx-4 text-center">
            <div class="mb-4">
                <div
                    class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-[#E50914] border-r-2 border-b-2 border-transparent">
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Processando pagamento</h3>
            <p class="text-gray-300">Por favor, aguarde enquanto concluímos sua assinatura...</p>
        </div>
    </div>

    <!-- Personalização Modal -->
    <div id="personalizacao"
        class="fixed inset-0 bg-black bg-opacity-80 flex flex-col justify-center items-center text-white z-50 hidden">
        <svg class="animate-spin h-10 w-10 text-red-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
        </svg>
        <p class="text-lg">Personalizando sua experiência premium...</p>
        <p class="text-sm mt-2 text-gray-400">Otimizando catálogo e recomendações 🔍</p>
    </div>

    <!-- Chat de Ajuda Rápida -->
    <button id="chat-button"
        class="fixed bottom-6 right-6 bg-[#E50914] hover:bg-[#B8070F] text-white px-4 py-2 rounded-full shadow-lg z-40">
        💬 Ajuda?
    </button>

    <div id="chat"
        class="hidden fixed bottom-20 right-6 bg-gray-900 text-white p-3 w-60 rounded-xl shadow-xl text-sm z-40">
        <div class="flex justify-between items-center mb-2">
            <strong>Central Rápida</strong>
            <button id="close-chat" class="text-gray-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <ul class="space-y-2 text-gray-300">
            <li class="hover:bg-gray-800 p-1 rounded cursor-pointer">📌 Cobrança</li>
            <li class="hover:bg-gray-800 p-1 rounded cursor-pointer">⏳ Liberação</li>
            <li class="hover:bg-gray-800 p-1 rounded cursor-pointer">🧾 Cancelamento</li>
        </ul>
    </div>
    <script src="{{ asset('js/custom/pay.js') }}"></script>


</body>

</html>
