<?php


namespace App\Services;


use Barryvdh\DomPDF\Facade as PDF;

class PdfGeneratorTexPass
{
    public static function generate($view, $data): string
    {
        $content = PDF::loadView($view, $data)->setOptions([
            'enable_html5_parser' => true,
        ])->output();

        $outFileName = 'id' . $data['passportId'] . '.pdf';
        $destination = implode(DIRECTORY_SEPARATOR, ['technical-passport', $outFileName]);

        if (!\Storage::disk('local')->exists($destination)) {
            \Storage::disk('local')->put($destination, $content);
        }

        $file_url = 'https://dev.uzteh.uz/public/technical-passport/id' . $data['passportId'];
//        $file_url = 'https://uzteh.uz/public/technical-passport/id' . $data['passportId'];

        return $file_url;
    }
}
