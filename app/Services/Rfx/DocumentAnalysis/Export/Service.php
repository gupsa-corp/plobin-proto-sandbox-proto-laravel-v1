<?php

namespace App\Services\Rfx\DocumentAnalysis\Export;

use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute($documentId, $format): array
    {
        $file = UploadedFile::find($documentId);
        
        if (!$file || !$file->analysis) {
            return [
                'success' => false,
                'message' => '분석 결과를 찾을 수 없습니다.'
            ];
        }

        // 실제 파일 생성 로직은 추후 구현
        // 현재는 mock 응답
        
        return [
            'success' => true,
            'message' => "분석 결과를 {$format} 형식으로 내보냈습니다.",
            'downloadUrl' => "/downloads/analysis_{$documentId}.{$format}"
        ];
    }
}