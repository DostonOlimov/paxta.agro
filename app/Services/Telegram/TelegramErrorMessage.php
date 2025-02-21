<?php

namespace App\Services\Telegram;

use GuzzleHttp\Client;

class TelegramErrorMessage extends TelegramTextMessage
{
    public function __construct(Client $httpClient, string $botToken, string $errorChatId)
    {
        parent::__construct($httpClient, $botToken, $errorChatId);
    }
}
