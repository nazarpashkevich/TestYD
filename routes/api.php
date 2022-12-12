<?php

use App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Opis\Closure\SerializableClosure;

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

Route::post('/telegram/webhook', [Api\Telegram\WebhookController::class, 'index'])->name('api.telegram.webhook');

Route::post('/trello/events/card/move', [Api\Trello\CardController::class, 'move'])->name('api.trello.webhook');

