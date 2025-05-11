<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Exibe a página de pagamento customizada para pay.snapphub
     */
    public function paySnapphub()
    {
        return view('pay');
    }
}
