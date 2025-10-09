<?php

namespace App\Services\Rfx\FileManager\DeleteFile;

use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute($fileId): array
    {
        $file = UploadedFile::find($fileId);
        
        if (!$file) {
            return [
                'success' => false,
                'message' => '파일을 찾을 수 없습니다.'
            ];
        }

        // 관련 분석 결과도 함께 삭제
        $file->analysis()?->delete();
        $file->delete();

        return [
            'success' => true,
            'message' => '파일이 삭제되었습니다.'
        ];
    }
}