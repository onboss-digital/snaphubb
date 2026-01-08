<?php

use Illuminate\Support\Facades\Route;
use Modules\Voting\Http\Controllers\VotingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    // Rotas de web foram movidas para Modules/Frontend/Routes/web.php
    // para estar dentro do middleware auth correto
});
