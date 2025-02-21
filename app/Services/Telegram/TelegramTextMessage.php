<?php
namespace App\Services\Telegram;

use App\Services\Telegram\Contracts\TelegramMessageSender;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class TelegramTextMessage implements TelegramMessageSender
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

    public function send(string $message): bool
    {
        return $this->sendRequest($this->chatId, $message);
    }

    protected function sendRequest(string $chatId, string $message): bool
    {
        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $response = $this->httpClient->post($url, [
                'json' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
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

