<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth')->except('handleRequest');
    }

    public function handleRequest()
    {
        $updates = Telegram::getWebhookUpdates();

        $chatId = $updates['message']['chat']['id'];
        $text = $updates['message']['text'];

        // Basic example to send a reply
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => "You said: $text"
        ]);

        return response()->json(['status' => 'success']);
    }
}
