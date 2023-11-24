<?php


namespace App\Services;


use Barryvdh\DomPDF\Facade as PDF;

class PdfGeneratorCertificate
{
    public static function generate($view, $data): string
    {
        $content = PDF::loadView($view, $data)->setOptions([
            'enable_html5_parser' => true,
        ])->output();

        $outFileName = 'id' . $data['certificateId'] . '.pdf';
        $destination = implode(DIRECTORY_SEPARATOR, ['technical-certificate', $outFileName]);

        if (!\Storage::disk('local')->exists($destination)) {
            \Storage::disk('local')->put($destination, $content);
        }

        $file_url = 'https://dev.uzteh.uz/public/technical-certificate/id' . $data['certificateId'];
//        $file_url = 'https://uzteh.uz/public/technical-certificate/id' . $data['certificateId'];

        return $file_url;
    }
}
