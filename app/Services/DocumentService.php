<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DocumentService
{
    private static $documents;

    public static function getDocument($documentId): ?Document
    {
        return static::getDocuments()->firstWhere('id', $documentId);
    }

    private static function getDocuments(): Collection
    {
        if (!static::$documents) {
            static::$documents = Cache::remember('documents', 60 * 60, function () {
                return Document::all();
            });
        }

        return static::$documents;
    }
}
