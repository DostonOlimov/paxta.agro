<?php

namespace App\Services\Telegram\Contracts;

interface TelegramMessageSender
{
    public function send(string $message): bool;
}
