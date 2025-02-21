<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Contracts\TelegramMessageSender;
use GuzzleHttp\Client;

class TelegramService
{
    protected TelegramMessageSender $messageSender;
    protected TelegramMessageSender $errorMessageSender;
    protected TelegramMessageSender $photoSender;

    public function __construct()
    {
        $httpClient = new Client();
        $botToken = config('services.telegram.bot_token') ?? throw new \InvalidArgumentException("Telegram bot token is missing.");
        $chatId = config('services.telegram.chat_id') ?? throw new \InvalidArgumentException("Telegram chat ID is missing.");
        $errorChatId = config('services.telegram.error_chat_id') ?? $chatId;

        $this->messageSender = new TelegramTextMessage($httpClient, $botToken, $chatId);
        $this->errorMessageSender = new TelegramErrorMessage($httpClient, $botToken, $errorChatId);
        $this->photoSender = new TelegramPhotoMessage($httpClient, $botToken, $chatId);
    }

    public function sendMessage(string $message): bool
    {
        return $this->messageSender->send($message);
    }

    public function sendErrorMessage(string $message): bool
    {
        return $this->errorMessageSender->send($message);
    }

    public function sendPhoto(string $photoUrl): bool
    {
        return $this->photoSender->send($photoUrl);
    }
}
