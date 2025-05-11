<?php


namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    /**
     * Exibe a página de pagamento customizada para pay.snapphub
     */
    public function sendCheckout(Request $request)
    {
        dd($request->all());
        // return view('pay');
        return redirect()->route('pay.snapphub');
    }
}