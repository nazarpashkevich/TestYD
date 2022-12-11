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

Route::get('/test', function(\App\Services\Telegram\SenderBot $senderBot) {
    $chatMembers = User::query()->byChat('-1001827268391')->get()->keyBy('trello_username')->toArray();
// get all lists and cards from board
    $trelloClient = new \App\Services\Trello\Client();
    $cards = $trelloClient->getListCards(env('TRELLO_LIST_ID'));
    foreach ($cards as $card) {
        foreach ($card['idMembers'] as $member) {
            // get detail member data
            $memberData = $trelloClient->getMemberData($member);
            if (isset($chatMembers[$memberData['username']])) {
                $chatMembers[$memberData['username']]['cards'][] = $card;
            }
        }
    }
    dd($chatMembers);
});

Route::get('/clear-cache', function() {
    dd(Artisan::call('migrate'));
    // return what you want
});
