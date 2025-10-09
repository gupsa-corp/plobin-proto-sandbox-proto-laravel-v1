<?php

namespace App\Services\Rfx\DocumentAnalysis\Regenerate;

use App\Models\Plobin\DocumentAnalysis;
use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute($documentId): array
    {
        $file = UploadedFile::find($documentId);
        
        if (!$file) {
            return [
                'success' => false,
                'message' => '파일을 찾을 수 없습니다.'
            ];
        }

        // 기존 분석 결과 상태를 analyzing으로 변경
        DocumentAnalysis::updateOrCreate(
            ['file_id' => $documentId],
            [
                'status' => 'analyzing',
                'analyzed_at' => now(),
                'summary' => null,
                'keywords' => null,
                'categories' => null,
                'confidence_score' => null,
                'extracted_data' => null,
                'recommendations' => null,
                'error_message' => null
            ]
        );

        // 파일 상태도 업데이트
        $file->update(['status' => 'analyzing']);

        return [
            'success' => true,
            'message' => '문서 분석을 다시 시작했습니다.'
        ];
    }
}