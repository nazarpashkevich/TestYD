<?php

namespace App\Services\Telegram\Commands;

use App\Models\User;
use App\Services\Telegram\Helpers\ChatHelper;
use App\Services\Telegram\Helpers\MessageTextHelper;
use App\Services\Telegram\SenderBot;

class ReportCommand implements AbstractCommand
{

    public function handle(array $data, SenderBot $bot): void
    {
        /*
         * 1. get chat members
         * 2. get from trello cards in every list
         * 3. make report
         */
        if (isset($data['chat']['id'])) {
            $chatMembers = User::query()->byChat($data['chat']['id'])->get()->keyBy('trello_username')->toArray();
            $this->compileCardsToUsers($chatMembers);
            $report = "Отчёт по нашей доске:\n";
            foreach ($chatMembers as $chatMember) {
                $report .= "    - задачи в работе {$chatMember['name']}" . (!empty($chatMember['tg_username']) ?
                    MessageTextHelper::strongText('(@' . $chatMember['tg_username'] . ')') : '') . ":\n";
                if (isset($chatMember['cards'])) {
                    foreach ($chatMember['cards'] as $card) {
                        $report .= "        · {$card['name']}\n";
                    }
                }
            }

            $bot->sendMessage($report, $data['chat']['id']);
        }
    }

    public function compileCardsToUsers(array &$users): void
    {
        // get all lists and cards from board
        $trelloClient = new \App\Services\Trello\Client();
        $cards = $trelloClient->getListCards(env('TRELLO_LIST_ID'));
        foreach ($cards as $card) {
            foreach ($card['idMembers'] as $member) {
                // get detail member data
                $memberData = $trelloClient->getMemberData($member);
                if (isset($users[$memberData['username']])) {
                    $users[$memberData['username']]['cards'][] = $card;
                }
            }
        }
    }
}
