<?php

namespace App\Services\FileUpload\ValidateFileSecurity;

use Illuminate\Http\UploadedFile as HttpUploadedFile;

class Service
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'text/csv',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    public function execute(HttpUploadedFile $file): void
    {
        // MIME 타입 검증
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('허용되지 않은 파일 형식입니다.');
        }

        // 파일 확장자 검증
        $extension = strtolower($file->getClientOriginalExtension());
        $dangerousExtensions = ['php', 'js', 'html', 'exe', 'bat', 'sh'];
        
        if (in_array($extension, $dangerousExtensions)) {
            throw new \Exception('보안상 허용되지 않은 파일 확장자입니다.');
        }

        // 파일명 보안 검증
        $filename = $file->getClientOriginalName();
        if (preg_match('/[<>:"|?*]/', $filename)) {
            throw new \Exception('파일명에 허용되지 않은 문자가 포함되어 있습니다.');
        }
    }
}