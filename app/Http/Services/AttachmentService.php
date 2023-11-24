<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService
{
    public function upload(UploadedFile $file, Model $entity): Attachment
    {
        $fileName = bin2hex(random_bytes(32)) . '.' .$file->getClientOriginalExtension();
        $folder = 'reason-files'  . DIRECTORY_SEPARATOR . now()->toDateString();
        $file->storeAs($folder, $fileName);

        return $entity->attachment()->create([
            'url' => $folder . DIRECTORY_SEPARATOR . $fileName
        ]);
    }
}
