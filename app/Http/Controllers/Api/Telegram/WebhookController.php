<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{

    public function index (\App\Services\Telegram\WebhookBot $bot): void
    {
        $bot->listen();
    }
}
