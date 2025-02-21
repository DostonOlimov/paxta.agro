<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Contracts\TelegramMessageSender;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class TelegramPhotoMessage implements TelegramMessageSender
{
    protected Client $httpClient;
    protected string $botToken;
    protected string $chatId;

    public function __construct(Client $httpClient, string $botToken, string $chatId)
    {
        $this->httpClient = $httpClient;
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    public function send(string $photoUrl): bool
    {
        return $this->sendRequest($this->chatId, $photoUrl);
    }

    protected function sendRequest(string $chatId, string $photoUrl): bool
    {
        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendPhoto";
            $response = $this->httpClient->post($url, [
                'json' => [
                    'chat_id' => $chatId,
                    'photo' => $photoUrl,
                    'caption' => "📷 New Photo",
                ]
            ]);

            return $this->isSuccessful($response);
        } catch (RequestException $e) {
            Log::error("Telegram API error: {$e->getMessage()}");
            return false;
        }
    }

    protected function isSuccessful(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 200;
    }
}
