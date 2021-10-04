<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/evento', [EventoController::class, 'checkEvent']);
Route::get('/balance/{id}', [EventoController::class, 'show']);
Route::post('/reset', function() {
    Artisan::call('migrate:fresh');
    return response()->json("Reseteado", 200);    
});
