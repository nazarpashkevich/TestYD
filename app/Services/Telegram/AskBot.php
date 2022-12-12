<?php

namespace App\Services\Telegram;

use Cache;
use Opis\Closure\SerializableClosure;

class AskBot
{
    const CACHE_TTL = 60 * 60 * 24;

    protected SenderBot $senderBot;
    protected string $question;
    protected string $chatId;
    protected \Closure $callback;


    public function __construct(SenderBot $bot, string $chatId)
    {
        $this->setSenderBot($bot);
        $this->setChatId($chatId);
    }

    public function listen(string $message, \Closure $callback): void
    {
        $this->setQuestion($message);
        $this->setCallback($callback);
        $this->ask();
        $this->storeConversation();
    }

    public function ask(): void
    {
        $this->getSenderBot()->sendMessage($this->getQuestion(), $this->getChatId());
    }

    public function storeConversation(): void
    {
        Cache::set('conversation_' . $this->getChatId(), [
            'question' => $this->getQuestion(),
            'chat_id' => $this->getChatId(),
            'callback' => serialize(new SerializableClosure($this->getCallback()))
        ], $this::CACHE_TTL);
    }

    public function checkConversation(): bool
    {
        $cacheKey = 'conversation_' . $this->getChatId();
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            if (!empty($data['question']) && !empty($data['chat_id']) && !empty($data['callback'])) {
                $this->setQuestion($data['question']);
                $this->setCallback(unserialize($data['callback'])->getClosure());

                return true;
            } else {
                $this->clearConversations();
            }
        }

        return false;
    }

    public function handle(array $data): void
    {
        $result = call_user_func($this->getCallback(), $data);
        if ($result !== false) {
            $this->clearConversations();
        } else {
            $this->ask();
        }
    }

    public function clearConversations(): void
    {
        Cache::delete('conversation_' . $this->getChatId());
    }

    /**
     * @return SenderBot
     */
    public function getSenderBot(): SenderBot
    {
        return $this->senderBot;
    }

    /**
     * @param SenderBot $senderBot
     */
    public function setSenderBot(SenderBot $senderBot): void
    {
        $this->senderBot = $senderBot;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @param string $chatId
     */
    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @return \Closure
     */
    public function getCallback(): \Closure
    {
        return $this->callback;
    }

    /**
     * @param \Closure $callback
     */
    public function setCallback(\Closure $callback): void
    {
        $this->callback = $callback;
    }
}
