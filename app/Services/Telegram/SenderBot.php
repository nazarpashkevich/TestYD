<?php

namespace App\Services\Telegram;

use App\Models\User;
use App\Services\Telegram\Helpers\RequestHelper;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Psy\Util\Json;

class SenderBot
{

    const API_METHOD_SEND_MESSAGE = 'sendMessage';


    public function getDbUser(array $userData): ?Model
    {
        if (isset($userData['id'])) {
            // identify by phone
            if (!$user = User::query()->firstWhere('tg_id', $userData['id'])) {
                $user = new User();
                $user->tg_id = $userData['id'];
            }
            $user->fill([
                'tg_username' => $userData['username'] ?? null,
                'name' => $userData['first_name'] . (!empty($userData['last_name']) ? ' ' . $userData['last_name'] : ''),
                'password' => \Hash::make(\Str::random(8))
            ]);
            $user->save();

            return $user;
        }

        return null;
    }

    public function sendMessage(string $message, string $chatId, array $options = []): void
    {
        $options['form_params'] = array_merge(
            $options['form_params'] ?? [],
            [
                'text' => $message,
                'chat_id' => $chatId,
                'parse_mode' => 'html'
            ]
        );

        RequestHelper::post(self::API_METHOD_SEND_MESSAGE, $options);
    }

    public function sendNeedStart(string $chatId): void
    {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => 'Привет', 'url' => env('TELEGRAM_BOT_LINK')]]
            ]
        ];
        $encodedKeyboard = Json::encode($keyboard);
        $this->sendMessage(
            "Сначала поздоровайся!",
            $chatId,
            ['form_params' => ['reply_markup' => $encodedKeyboard]]
        );
    }
}
