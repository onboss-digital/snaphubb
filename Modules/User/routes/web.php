<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Backend\RankingController;
use Modules\User\Http\Controllers\Backend\UsersController;



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
/*
 *
 * Backend Routes
 *
 * --------------------------------------------------------------------
 */
Route::group(['prefix' => 'app', 'as' => 'backend.', 'middleware' => ['auth', 'admin']], function () {
  /*
   * These routes need view-backend permission
   * (good if you want to allow more than one group in the backend,
   * then limit the backend features by different roles or permissions)
   *
   * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
   */

  /*
   *
   *  Backend Users Routes
   *
   * ---------------------------------------------------------------------
   */

  Route::get('app/users/{type}', [UsersController::class, 'index'])->name('users.soon-to-exipre');

  Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get("index_list", [UsersController::class, 'index_list'])->name("index_list");
    Route::get("index_data", [UsersController::class, 'index_data'])->name("index_data");
    Route::get('export', [UsersController::class, 'export'])->name('export');
    Route::post('update-status/{id}', [UsersController::class, 'update_status'])->name('update_status');
    Route::post('bulk-action', [UsersController::class, 'bulk_action'])->name('bulk_action');
    Route::get('changepassword/{id}', [UsersController::class, 'changepassword'])->name('changepassword');
    Route::post('update-password/{id}', [UsersController::class, 'updatePassword'])->name('update_password');
    Route::get('details/{id}', [UsersController::class, 'details'])->name('details');


    /*Ranking routes*/
    Route::group(['prefix' => 'ranking', 'as' => 'ranking.'], function () {
      Route::get('/', [RankingController::class, 'index'])->name('index_list');
      Route::get('create', [RankingController::class, 'create'])->name('create');
      Route::post('store', [RankingController::class, 'store'])->name('store');
      Route::post('bulk-action', [RankingController::class, 'bulk_action'])->name('bulk_action');
      Route::get('/index_data', [RankingController::class, 'index_data'])->name('index_data');
      Route::get('export', [RankingController::class, 'export'])->name('export');
      Route::post('restore/{id}', [RankingController::class, 'restore'])->name('restore');
      Route::delete('force-delete/{id}', [RankingController::class, 'forceDelete'])->name('force_delete');
      Route::post('update-status/{id}', [RankingController::class, 'update_status'])->name('update_status');

      Route::get('/trashed', [RankingController::class, 'trashed'])->name('trashed');
      Route::post('bulk-action', [RankingController::class, 'bulk_action'])->name('bulk_action');

      Route::get('edit/{id}', [RankingController::class, 'edit'])->name('edit');

      Route::put('update/{id}', [RankingController::class, 'update'])->name(name: 'update');
      Route::delete('destroy/{id}', [RankingController::class, 'destroy'])->name('destroy');

    });


  });
  Route::resource("users", UsersController::class);
  Route::post('send-email', [UsersController::class, 'sendEmail'])->name('send.email');




});



