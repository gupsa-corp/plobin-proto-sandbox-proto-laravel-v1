<?php

namespace App\Services\FileUpload\GenerateUniqueFilename;

use Illuminate\Http\UploadedFile as HttpUploadedFile;

class Service
{
    public function execute(HttpUploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        return "{$timestamp}_{$originalName}.{$extension}";
    }
}