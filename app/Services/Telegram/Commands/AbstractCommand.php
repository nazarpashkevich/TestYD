<?php

namespace App\Services\Telegram\Commands;

use App\Services\Telegram\SenderBot;
use Illuminate\Http\Response;

interface AbstractCommand
{
    public function handle(array $data, SenderBot $bot): void;
}
