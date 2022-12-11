<?php

namespace App\Http\Controllers\Api\Trello;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Trello\CardMoveRequest;
use App\Services\Telegram\Helpers\MessageTextHelper;
use App\Services\Telegram\SenderBot;
use Illuminate\Http\Response;

class CardController extends Controller
{

    public function move(CardMoveRequest $request, SenderBot $bot): Response
    {
        $msgHelper = new MessageTextHelper();
        $message = "Кто-то передвинул карточку:\n";
        $message .= "    - Название: {$msgHelper->strongText($request->get('card'))},\n";
        $message .= "    - Ссылка: {$msgHelper->strongText($request->get('link'))},\n";
        $message .= "    - Передвинули сюда: {$msgHelper->strongText($request->get('cardlistname'))}
        ";
        $bot->sendMessage($message, env('TELEGRAM_BOT_CHAT_ID'));

        return new Response();
    }
}
