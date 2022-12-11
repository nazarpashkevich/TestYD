<?php

namespace App\Services\Telegram\Helpers;

class MessageTextHelper
{

    public static function strongText(string $string): string
    {
        return "<strong>$string</strong>";
    }
}
