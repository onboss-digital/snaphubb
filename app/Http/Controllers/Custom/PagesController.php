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
     * AJAX endpoint to calculate price based on plan, bump, coupon, and currency
     */
    public function priceCalculate(Request $request)
    {
        $locale = app()->getLocale();
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

        $currency = $request->input('currency', 'BRL');
        $planKey = $request->input('plan', 'monthly');
        $orderBump = $request->input('order_bump', false);
        $coupon = $request->input('coupon', null);

        // Get plan price
        $plan = $plans[$locale][$planKey] ?? $plans['br']['monthly'];
        $planPrice = $plan['price'];
        $bumpPrice = $orderBump ? $bump['price'] : 0;

        // Coupon logic (example: DESCONTO20 = 20%, PROMO10 = 10%)
        $discount = 0;
        if ($coupon) {
            $coupon = strtoupper(trim($coupon));
            if ($coupon === 'DESCONTO20') {
                $discount = 0.20;
            } elseif ($coupon === 'PROMO10') {
                $discount = 0.10;
            }
        }

        $subtotal = $planPrice + $bumpPrice;
        $discountAmount = $subtotal * $discount;
        $total = $subtotal - $discountAmount;

        // Currency conversion (simple static, for demo)
        $conversionRates = [
            'BRL' => 1,
            'USD' => 0.20,
            'EUR' => 0.18,
        ];
        $rate = $conversionRates[$currency] ?? 1;
        $planPriceConv = round($planPrice * $rate, 2);
        $bumpPriceConv = round($bumpPrice * $rate, 2);
        $originalPriceConv = round($originalPrice * $rate, 2);
        $discountAmountConv = round($discountAmount * $rate, 2);
        $totalConv = round($total * $rate, 2);

        return response()->json([
            'plan_price' => $planPriceConv,
            'bump_price' => $bumpPriceConv,
            'original_price' => $originalPriceConv,
            'discount_amount' => $discountAmountConv,
            'total' => $totalConv,
            'currency' => $currency,
            'discount_percent' => $discount * 100,
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
