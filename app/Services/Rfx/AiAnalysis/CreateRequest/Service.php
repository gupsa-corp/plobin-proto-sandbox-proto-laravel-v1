<?php

namespace App\Services\Rfx\AiAnalysis\CreateRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(string $ocrRequestId): array
    {
        try {
            // 1. OCR 데이터 조회
            $getResultService = new \App\Services\Rfx\DocumentAnalysis\GetResult\Service();
            $ocrResult = $getResultService->execute($ocrRequestId);

            if (!isset($ocrResult['ocrRawData'])) {
                return [
                    'success' => false,
                    'message' => 'OCR 데이터를 찾을 수 없습니다.'
                ];
            }

            $ocrData = $ocrResult['ocrRawData'];

            // 2. 중복 요청 체크 (최근 5분 이내 동일 문서에 대한 pending/processing 요청)
            $existingRequest = DB::table('rfx_ai_analysis_requests')
                ->where('file_id', $ocrRequestId)
                ->whereIn('status', ['pending', 'processing'])
                ->where('requested_at', '>=', now()->subMinutes(5))
                ->first();

            if ($existingRequest) {
                return [
                    'success' => false,
                    'message' => '이미 처리 중인 AI 분석 요청이 있습니다. 잠시 후 다시 시도해주세요.',
                    'request_id' => $existingRequest->id
                ];
            }

            // 3. AI 분석 요청 레코드 생성
            $requestId = DB::table('rfx_ai_analysis_requests')->insertGetId([
                'file_id' => $ocrRequestId,
                'file_name' => $ocrData['original_filename'] ?? 'Unknown',
                'file_type' => $ocrData['file_type'] ?? 'unknown',
                'analysis_type' => 'ai_document_analysis',
                'status' => 'pending',
                'progress' => 0,
                'result' => null,
                'error_message' => null,
                'requested_at' => now(),
                'started_at' => null,
                'completed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('AI 분석 요청 생성 완료', [
                'request_id' => $requestId,
                'ocr_request_id' => $ocrRequestId,
                'user_id' => Auth::id()
            ]);

            // 4. Queue Job 디스패치
            \App\Jobs\Rfx\AiAnalysis\Generate\Jobs::dispatch($requestId, $ocrRequestId);

            return [
                'success' => true,
                'message' => 'AI 분석 요청이 생성되었습니다. 백그라운드에서 처리 중입니다.',
                'request_id' => $requestId
            ];

        } catch (\Exception $e) {
            Log::error('AI 분석 요청 생성 실패', [
                'ocr_request_id' => $ocrRequestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'AI 분석 요청 생성 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
}
