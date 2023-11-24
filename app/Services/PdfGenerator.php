<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;

class PdfGenerator
{
    public static function generate($view, $data): string
    {
        $content = PDF::loadView($view, $data)->setOptions([
            'enable_html5_parser' => true,
        ])->output();

        $outFileName = md5($content) . '.pdf';
        $destination = implode(DIRECTORY_SEPARATOR, ['notary', now()->toDateString(), $outFileName]);
        if (!\Storage::disk('local')->exists($destination)) {
            \Storage::disk('local')->put($destination, $content);
        }

        return $destination;
    }

    public static function replaceToBase64($data): array
    {
        if (Arr::has($data, 'pdf_url')) {
            $key = Arr::get($data, 'pdf_key', 'pdf');

            $data[$key] = $data['pdf_url'] ? base64_encode(\Storage::disk('local')->get($data['pdf_url'])) : null;
        }

        return Arr::except($data, ['pdf_url', 'pdf_key']);
    }
}

