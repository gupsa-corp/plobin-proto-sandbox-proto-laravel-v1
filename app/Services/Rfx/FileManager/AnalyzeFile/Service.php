<?php

namespace App\Services\Rfx\FileManager\AnalyzeFile;

use App\Models\Plobin\UploadedFile;
use App\Models\Plobin\DocumentAnalysis;

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

        // 파일 상태를 분석 중으로 변경
        $file->update(['status' => 'analyzing']);

        // 분석 레코드 생성 또는 업데이트
        DocumentAnalysis::updateOrCreate(
            ['file_id' => $fileId],
            [
                'status' => 'analyzing',
                'analyzed_at' => now()
            ]
        );

        return [
            'success' => true,
            'message' => '파일 분석이 시작되었습니다.'
        ];
    }
}