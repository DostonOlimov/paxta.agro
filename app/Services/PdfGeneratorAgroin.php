<?php


namespace App\Services;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;

class PdfGeneratorAgroin
{
    public static function generate($view, $data): string
    {
        $content = PDF::loadView($view, $data)->setOptions([
            'enable_html5_parser' => true,
        ])->output();

        $outFileName = $data['appealId'] . '&letter:' . $data['letterId'] . '.pdf';
        $destination = implode(DIRECTORY_SEPARATOR, ['agroin', $outFileName]);

        if (!\Storage::disk('local')->exists($destination)) {
            \Storage::disk('local')->put($destination, $content);
        }

        $file_url = 'https://dev.uzteh.uz/public/agroin-appeal/' . $data['appealId'] . '/letter' . $data['letterId'];
//        $file_url = 'https://uzagroteh.uz/public/agroin-appeal/' . $data['appealId'] . '/letter' . $data['letterId'];

        return $file_url;
    }

    public static function replaceToBase64($file): string
    {
        return base64_encode(\Storage::disk('local')->get($file));
    }
}

