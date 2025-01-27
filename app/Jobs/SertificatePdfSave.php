<?php

namespace App\Jobs;

use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertificatePdfSave implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appId;
    protected $result;
    protected $count;
    protected $number;
    protected $date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$result,$number,$date)
    {
        $this->appId = $id;
        $this->result = $result;
        $this->count = count($result);
        $this->number = $number;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        for ($i = 1; $i < $this->count; $i++) {
            //find application by id
            $application = Application::find($this->appId);
            //getting final result data
            $group = $this->result[$i];
            //setting sertificate number
            $this->number++;
            $sertNumber = $this->number + $i;
            $formattedDate = $this->date;
            $currentYear = date('Y');
            //route for qrcode
            $route = route('sifat_sertificate.download', ['id' => $this->appId, 'type' => $i]);
            //generate qr code
            $qrCode = base64_encode(QrCode::format('png')->size(100)->generate($route));
            //generate pdf file
            $pdf = Pdf::loadView('sertificate_protocol.sertificate_pdf', compact('application','group', 'sertNumber', 'currentYear','formattedDate', 'qrCode'));
            //save pdf file
            $pdf->save(storage_path("app/public/sifat_sertificates/certificate_{$this->appId}_{$i}.pdf"));
        }
    }
}
