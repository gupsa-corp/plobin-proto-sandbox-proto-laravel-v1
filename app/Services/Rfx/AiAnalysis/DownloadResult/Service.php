<?php

namespace App\Services\Rfx\AiAnalysis\DownloadResult;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function execute(int $requestId): array
    {
        try {
            // 1. 요청 ID로 분석 결과 조회
            $request = DB::table('rfx_ai_analysis_requests')
                ->where('id', $requestId)
                ->first();

            if (!$request) {
                return [
                    'success' => false,
                    'message' => '요청을 찾을 수 없습니다.',
                ];
            }

            if ($request->status !== 'completed') {
                return [
                    'success' => false,
                    'message' => '아직 분석이 완료되지 않았습니다.',
                ];
            }

            // 2. JSON 파일 내용 생성
            $result = json_decode($request->result, true);
            $fileName = "analysis_result_{$requestId}.json";
            $content = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // 3. 내용을 직접 반환 (Livewire 이벤트로 처리)
            return [
                'success' => true,
                'message' => '결과 파일을 준비했습니다.',
                'content' => $content,
                'fileName' => $fileName,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '다운로드 준비에 실패했습니다: ' . $e->getMessage(),
            ];
        }
    }
}
