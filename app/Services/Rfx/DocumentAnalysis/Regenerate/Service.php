<?php

namespace App\Services\Rfx\DocumentAnalysis\Regenerate;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute($documentId, $reason = null): array
    {
        try {
            // Queue Job 디스패치 (비동기 처리)
            \App\Jobs\Rfx\DocumentAnalysis\Reanalyze\Jobs::dispatch(
                $documentId,
                Auth::id(),
                $reason
            );

            Log::info('재분석 Job 디스패치 완료', [
                'documentId' => $documentId,
                'userId' => Auth::id()
            ]);

            return [
                'success' => true,
                'message' => '문서 재분석 요청이 접수되었습니다. 백그라운드에서 처리 중입니다.'
            ];

        } catch (\Exception $e) {
            Log::error('재분석 Job 디스패치 실패', [
                'documentId' => $documentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => '재분석 요청 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
}