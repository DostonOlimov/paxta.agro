<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $filename;
    protected $exportRequestId;

    public function __construct(string $filename, int $exportRequestId)
    {
        $this->filename = $filename;
        $this->exportRequestId = $exportRequestId;
    }

    public function via($notifiable): array
    {
        return ['database']; // Only use database to avoid mail server issues
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Sizning Excel eksport faylingiz tayyor. Yuklab olishingiz mumkin.',
            'filename' => $this->filename,
            'download_url' => route('excel.download', ['filename' => $this->filename]),
            'export_request_id' => $this->exportRequestId,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Export is Ready')
            ->line('Your Excel export has been completed successfully.')
            ->line("Filename: {$this->filename}")
            ->action('Download Export', route('excel.download', ['filename' => $this->filename]))
            ->line('This download link will be available for 24 hours.');
    }

    public function toArray($notifiable): array
    {
        return [
            'filename' => $this->filename,
            'export_request_id' => $this->exportRequestId,
        ];
    }
}