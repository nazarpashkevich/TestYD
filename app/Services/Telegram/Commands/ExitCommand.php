<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\AskBot;
use App\Services\Telegram\SenderBot;
use App\Services\Telegram\WebhookBot;
use Illuminate\Http\Response;

class ExitCommand implements AbstractCommand
{

    public function handle(array $data, SenderBot $bot): void
    {
        /*
         * clear all conversation
         */

        if (isset($data['from']['id'])) {
            (new AskBot($bot, $data['from']['id']))->clearConversations();
        }
    }
}
