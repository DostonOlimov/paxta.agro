<?php
namespace App\Http\Controllers;

use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth')->except('setWebhook');
    }

    public function setWebhook()
    {
        $url = 'https://yourdomain.com/telegram/webhook';
        Telegram::setWebhook(['url' => $url]);

        return 'Webhook set!';
    }
}
