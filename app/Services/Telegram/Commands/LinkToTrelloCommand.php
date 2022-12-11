<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\AskBot;
use App\Services\Telegram\Conversations\AskTrelloEmailConversation;
use App\Services\Telegram\SenderBot;
use App\Services\Telegram\WebhookBot;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;

class LinkToTrelloCommand implements AbstractCommand
{

    public function handle(array $data, SenderBot $bot): void
    {
        /*
         * 1. ask email
         * 2. set email to user
         */

        if (isset($data['from']['id'])) {
            try {
                (new AskBot($bot, $data['from']['id']))->listen(
                    'Подскажи свой Trello username?',
                    function ($response) {
                        $senderBot = new SenderBot();
                        if (!empty($response['text'])) {
                            if (!User::query()->where('trello_username', $response['text'])->exists()) {
                                if ($user = $senderBot->getDbUser($response['from'])) {
                                    $user->trello_username = $response['text'];
                                    $user->save();
                                    $senderBot->sendMessage('Привязал', $response['from']['id']);

                                    return true;
                                }
                            }
                        }

                        $senderBot->sendMessage(
                            'Упс, что то не так, или этот username уже привязан к другому аккаунту',
                            $response['from']['id']
                        );

                        return false;
                    }
                );
            } catch (ClientException $clientException) {
                if ($data['from']['id'] !== $data['chat']['id']) {
                    $bot->sendNeedStart($data['chat']['id']);
                }
            }
        }
    }


}
