<?php

namespace App\Services\Rfx\FileUpload\GetRecentUploads;

use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute(): array
    {
        $files = UploadedFile::with('uploader')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->original_name,
                    'size' => $file->formatted_file_size,
                    'type' => pathinfo($file->original_name, PATHINFO_EXTENSION),
                    'uploadedAt' => $file->created_at->format('Y-m-d H:i:s'),
                    'status' => $file->status,
                    'uploader' => $file->uploader ? $file->uploader->name : 'Unknown'
                ];
            })
            ->toArray();

        return $files;
    }
}