<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class PagesController extends Controller
{
    public $availableLanguages = [
        'br' => '🇧🇷 Português',
        'en' => '🇺🇸 English',
        'es' => '🇪🇸 Español',
    ];

    /**
     * Exibe a página de pagamento customizada para pay.snapphub
     */
    public function paySnapphub()
    {
        $plans = [
            'br' => [
                'monthly' => [
                    'hash' => 'penev',
                    'label' => __('payment.monthly'),
                    'price' => 60.00,
                ],
                'quarterly' => [
                    'hash' => 'velit nostrud dolor in deserunt',
                    'label' => __('payment.quarterly'),
                    'price' => 265.00,
                ],
                'annual' => [
                    'hash' => 'cupxl',
                    'label' => __('payment.annual'),
                    'price' => 783.00,
                ]
            ],
            'en' => [
                'monthly' => [
                    'hash' => 'penev',
                    'label' => __('payment.monthly'),
                    'price' => 60.00,
                ],
                'quarterly' => [
                    'hash' => 'velit nostrud dolor in deserunt',
                    'label' => __('payment.quarterly'),
                    'price' => 265.00,
                ],
                'annual' => [
                    'hash' => 'cupxl',
                    'label' => __('payment.annual'),
                    'price' => 783.00,
                ]
            ],
            'es' => [
                'monthly' => [
                    'hash' => 'penev',
                    'label' => __('payment.monthly'),
                    'price' => 60.00,
                ],
                'quarterly' => [
                    'hash' => 'velit nostrud dolor in deserunt',
                    'label' => __('payment.quarterly'),
                    'price' => 265.00,
                ],
                'annual' => [
                    'hash' => 'cupxl',
                    'label' => __('payment.annual'),
                    'price' => 783.00,
                ]
            ]
        ];

        $bump = [
            'id' => 4,
            'title' => 'Acesso Exclusivo',
            'description' => 'Acesso a conteúdos ao vivo e eventos',
            'price' => 9.99,
            'hash' => 'xwe2w2p4ce_lxcb1z6opc',
        ];

        $originalPrice = 89.90;

        return view('custom.pay', [
            'locale' =>  app()->getLocale(),
            'availableLanguages' => $this->availableLanguages,
            'plans' => $plans,
            'bump' => $bump,
            'originalPrice' => $originalPrice,
        ]);
    }

    /**
     * Change language for the payment page
     */
    public function changeLanguage(Request $request)
    {
        if (key_exists($request->lang, $this->availableLanguages)) {
            app()->setLocale($request->lang);
        }
        return $this->paySnapphub();
    }
}
