<?php

namespace App\Services\Rfx\FileDownload\GetFileInfo;

use App\Models\Plobin\UploadedFile;
use App\Http\Controllers\Rfx\FileDownload\Response;

class Service
{
    public function execute(int $fileId): array
    {
        $file = UploadedFile::with('uploader')->find($fileId);
        
        if (!$file) {
            return Response::notFound();
        }

        return [
            'success' => true,
            'data' => [
                'id' => $file->id,
                'original_name' => $file->original_name,
                'file_size' => $file->file_size,
                'formatted_size' => $file->formatted_file_size,
                'mime_type' => $file->mime_type,
                'status' => $file->status,
                'download_count' => $file->download_count,
                'uploaded_at' => $file->created_at->format('Y-m-d H:i:s'),
                'uploader' => $file->uploader ? $file->uploader->name : null,
                'description' => $file->description,
                'tags' => $file->tags
            ]
        ];
    }
}