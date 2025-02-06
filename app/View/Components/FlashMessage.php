<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FlashMessage extends Component
{
    private ?string $message;
    private ?string $downloadUrl;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->message = session('message');
        $this->downloadUrl = $this->resolveDownloadUrl();
    }

    /**
     * Get the translated message.
     */
    private function getTranslatedMessage(): ?string
    {
        return self::messageMappings()[$this->message] ?? $this->message;
    }

    /**
     * Get the download URL if applicable.
     */
    private function resolveDownloadUrl(): ?string
    {
        $generatedAppId = request()->get('generatedAppId', 1);
        $downloadRoutes = [
            'Certificate saved!' => route('sifat_sertificate.download', $generatedAppId),
            'Protocol saved!' => route('laboratory_protocol.download', $generatedAppId),
        ];

        return $downloadRoutes[$this->message] ?? null;
    }

    /**
     * Message translation mappings.
     */
    private static function messageMappings(): array
    {
        return [
            'Successfully Submitted' => trans('app.Successfully Submitted'),
            'Successfully Updated' => trans('app.Successfully Updated'),
            'Successfully Deleted' => trans('app.Successfully Deleted'),
            'Cannot Deleted' => trans('app.Cannot Deleted'),
            'Duplicate Data' => trans('app.Duplicate Data'),
            'Certificate saved!' => 'Sertifikat fayli saqlandi! Yuklab olishingiz mumkin.',
            'Protocol saved!' => 'Bayonnoma fayli saqlandi! Yuklab olishingiz mumkin.',
        ];
    }

    /**
     * Get the view that represents the component.
     */
    public function render()
    {
        return view('components.flash-message', [
            'translatedMessage' => $this->getTranslatedMessage(),
            'downloadUrl' => $this->downloadUrl,
        ]);
    }
}

