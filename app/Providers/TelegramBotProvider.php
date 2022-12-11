<?php

namespace App\Providers;

use App\Services\Telegram\SenderBot;
use App\Services\Telegram\WebhookBot;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TelegramBotProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(WebhookBot::class, function (\Illuminate\Foundation\Application $app): WebhookBot {
            return new WebhookBot($app['config'], $app['request']);
        });

        $this->app->singleton(SenderBot::class, function (\Illuminate\Foundation\Application $app): SenderBot {
            return new SenderBot();
        });
    }
}
