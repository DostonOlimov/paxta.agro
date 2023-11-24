<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Http\Controllers\Controller;

final class AttachmentsController extends Controller
{
    public function download($id)
    {
        $attachment = Attachment::findOrFail($id);
        return response()->download(storage_path('app/' . $attachment->url));
    }
}
