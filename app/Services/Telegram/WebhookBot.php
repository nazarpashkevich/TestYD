<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Commands\AbstractCommand;
use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhookBot
{
    protected Request $request;
    protected Repository $config;

    public function __construct(Repository $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    public function listen(): Response
    {
        if ($data = $this->getDataFromRequest()) {
            $senderBot = \App::make(SenderBot::class);
            $askBot = new AskBot($senderBot, $data['chat']['id']);

            if ($this->isCommandRequest()) {
                // if command - delete any conversations
                $askBot->clearConversations();
                if ($command = $this->getCommand()) {
                    (new $command())->handle($data, $senderBot);
                }
            } elseif ($this->checkNewUserEvent()) { // check if new user added
                $this->newUserHandler($data, $senderBot);
            } elseif ($this->checkLeftUserEvent()) { // check if in conversation
                $this->leftUserHandler($data, $senderBot);
            } elseif ($askBot->checkConversation()) { // check if in conversation
                $askBot->handle($data);
            }
        }

        return new Response();
    }

    public function checkLeftUserEvent(): bool
    {
        return (bool)!empty($this->getRequest()->get('message')['left_chat_member']);
    }

    public function checkNewUserEvent(): bool
    {
        return (bool)!empty($this->getRequest()->get('message')['new_chat_members']);
    }

    public function newUserHandler(array $data, SenderBot $bot): void
    {
        foreach ($data['new_chat_members'] as $user) { // save chat relation
            $dbUser = $bot->getDbUser($user);
            $dbUser->addChat($data['chat']['id']);
        }
    }

    public function leftUserHandler(array $data, SenderBot $bot): void
    {
        $dbUser = $bot->getDbUser($data['left_chat_member']); // delete chat relation
        $dbUser->removeChat($data['chat']['id']);
    }

    public function isCommandRequest(): bool
    {
        return (bool)isset($this->getRequest()->get('message')['entities'][0]['type']) &&
            $this->getRequest()->get('message')['entities'][0]['type'] == 'bot_command';
    }

    public function getCommand(): ?string
    {
        $commandName = \Str::studly(\Str::substr(explode('@', $this->getRequest()->get('message')['text'])[0], 1));

        if ($commandName) {
            $fullClassName = '\\' . __NAMESPACE__ . '\Commands\\' . $commandName . 'Command';
            if (class_exists($fullClassName) && in_array(AbstractCommand::class, class_implements($fullClassName))) {
                return $fullClassName;
            }
        }

        return null;
    }

    public function getDataFromRequest(): array
    {
        if ($this->getRequest()->has('message')) {
            return $this->getRequest()->get('message');
        }

        return [];
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Repository
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * @param Repository $config
     */
    public function setConfig(Repository $config): void
    {
        $this->config = $config;
    }
}
