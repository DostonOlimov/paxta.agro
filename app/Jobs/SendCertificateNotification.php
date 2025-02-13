<?php

namespace App\Jobs;

use App\Models\FinalResult;
use App\Models\SifatSertificates;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use phpseclib3\Math\PrimeField\Integer;

class SendCertificateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $certificate;
    protected $message;
    protected $groupResults;
    protected $result;

    /**
     * Create a new job instance.
     * @param SifatSertificates $certificate
     * @param $dalolatnomaId
     */
    public function __construct(SifatSertificates $certificate, $dalolatnomaId)
    {
        $this->certificate = $certificate;

        // Group results by sort
        $this->groupResults = $this->groupResultsBySort($dalolatnomaId);

        // Ensure the certificate's `chp` index exists in grouped results
        $this->result = $this->groupResults[$this->certificate->chp - 1] ?? [];

        // Generate formatted certificate number
        $this->formattedCertificateNumber = $this->formatCertificateNumber();

        // Build the notification message
        $this->message = $this->buildNotificationMessage();
    }

    /**
     * Execute the job.
     *
     * @param TelegramService $telegramService
     */
    public function handle(TelegramService $telegramService)
    {
        $telegramService->sendMessage($this->message);
    }

    /**
     * Group final results by sort.
     *
     * @param int $dalolatnomaId
     * @return \Illuminate\Support\Collection
     */
    private function groupResultsBySort($dalolatnomaId)
    {
        return FinalResult::with([
            'dalolatnoma.laboratory_result',
            'dalolatnoma.laboratory_final_results.director'
        ])
            ->where('dalolatnoma_id', $dalolatnomaId)
            ->get()
            ->groupBy('sort')
            ->values();
    }

    /**
     * Format the certificate number.
     *
     * @return string
     */
    private function formatCertificateNumber(): string
    {
        $regionSeries = $this->certificate->application->prepared->region->series;
        $certificateNumber = substr(1000000 + $this->certificate->number, 1);

        return $regionSeries . '-' . $certificateNumber;
    }

    /**
     * Build the notification message.
     *
     * @return string
     */
    private function buildNotificationMessage(): string
    {
        $message = sprintf(
            "🆕 *Yangi Sertifikat Yaratildi!* 🎉\n\n" .
            "📜 *Sertifikat Raqami:* `%s`\n" .
            "🌾 *Mahsulot Turi:* `%s`\n" .
            "📅 *Bayonnoma Sana:* `%s`\n" .
            "👤 *Buyurtmachi:* `%s`\n" .
            "🛠 *Xodim:* `%s`\n",
            $this->formattedCertificateNumber,
            $this->certificate->application->crops->name->name,
            $this->certificate->application->tests->dalolatnoma->laboratory_final_results->date,
            $this->certificate->application->organization->name,
            $this->formatDirectorName()
        );

        // Add table header
        $message .= "📊 *Tafsilotlar:* \n";
        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= "| №  | Nomi         | Qiymat  |\n";
        $message .= "|----|-------------|---------|\n";

        // Add dynamic rows
        foreach ($this->result as $index => $detail) {
            $message .= sprintf(
                "| %d️⃣ | %s | `%s` |\n",
                $index + 1,
                $detail->count ?? 'N/A', // Handle null values gracefully
                number_format($detail->amount ?? 0, 2, '.', ' ') // Format amount with fallback
            );
        }

        // Add footer line
        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= sprintf("📅 *Yaratilgan Sana:* `%s`", now()->format('Y-m-d H:i'));

        return $message;
    }

    /**
     * Format the director's name.
     *
     * @return string
     */
    private function formatDirectorName(): string
    {
        $director = $this->certificate->application->tests->dalolatnoma->laboratory_final_results->director;

        if (!$director) {
            return 'N/A';
        }

        return trim($director->lastname . ' ' . $director->name);
    }
}
