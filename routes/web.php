<?php

use App\Http\Controllers\CloudLXController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [CloudLXController::class, 'index']);
Route::get('/{serviceId}', [CloudLXController::class, 'show']);
