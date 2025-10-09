<?php

namespace App\Services\FileUpload\ValidateFileUpload;

use Illuminate\Http\UploadedFile as HttpUploadedFile;

class Service
{
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB per file

    public function execute(HttpUploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('파일 업로드 중 오류가 발생했습니다.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('파일 크기가 너무 큽니다. 최대 10MB까지 허용됩니다.');
        }

        if (empty($file->getClientOriginalName())) {
            throw new \Exception('파일명이 필요합니다.');
        }
    }
}