<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\SenderBot;

class StartCommand implements AbstractCommand
{

    public function handle(array $data, SenderBot $bot): void
    {
        /*
         * 1. save user to db
         * 2. say hello {username}
         */
        /**
         * @var User $user
         */
        if ($user = $bot->getDbUser($data['from'])) {
            $chatId = $data['chat']['id'];
            if ($chatId !== $user->tg_id) { // if send in group
                $user->addChat($chatId);
            }
            $bot->sendMessage("Привет, {$user->name}!", $data['chat']['id']);
        }
    }
}
