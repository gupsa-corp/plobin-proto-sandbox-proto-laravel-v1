<?php

namespace App\Services\Rfx\FileManager\AnalyzeFile;

use App\Models\Plobin\UploadedFile;
use App\Models\Plobin\DocumentAnalysis;
use App\Jobs\Rfx\AnalyzeDocument\Jobs as AnalyzeDocumentJob;
use Illuminate\Support\Facades\Log;

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

        // 이미 분석 중인지 확인
        if (in_array($file->status, ['analyzing', 'queued'])) {
            return [
                'success' => false,
                'message' => '이미 분석이 진행 중입니다.'
            ];
        }

        try {
            // 파일 상태를 분석 대기로 변경
            $file->update(['status' => 'queued']);

            // 분석 레코드 생성 또는 업데이트
            DocumentAnalysis::updateOrCreate(
                ['file_id' => $fileId],
                [
                    'status' => 'queued',
                    'queued_at' => now()
                ]
            );

            // 큐에 분석 Job 추가
            AnalyzeDocumentJob::dispatch($fileId);
            
            Log::info("Document analysis job queued for file ID: {$fileId}");

            return [
                'success' => true,
                'message' => '파일 분석이 큐에 추가되었습니다. 잠시 후 분석이 시작됩니다.'
            ];

        } catch (\Exception $e) {
            Log::error("Failed to queue document analysis for file ID: {$fileId}", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => '분석 요청 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
}