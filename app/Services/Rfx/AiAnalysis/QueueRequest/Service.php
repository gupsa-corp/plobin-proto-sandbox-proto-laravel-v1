<?php

namespace App\Services\Rfx\AiAnalysis\QueueRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Jobs\Rfx\ProcessOcrAnalysis\Jobs as ProcessOcrAnalysisJob;

class Service
{
    public function execute(string $fileId): array
    {
        try {
            // 1. 파일 정보 조회 (임시 데이터 - 실제로는 DB에서 조회)
            // TODO: 실제 파일 테이블에서 조회하도록 수정 필요
            $file = $this->getFileInfo($fileId);

            if (!$file) {
                return [
                    'success' => false,
                    'message' => '파일을 찾을 수 없습니다.',
                ];
            }

            // 2. AI 분석 요청 레코드 생성
            $requestId = DB::table('rfx_ai_analysis_requests')->insertGetId([
                'file_id' => $fileId,
                'file_name' => $file['name'],
                'file_type' => $file['type'],
                'analysis_type' => 'OCR 텍스트 추출',
                'status' => 'pending',
                'progress' => 0,
                'requested_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. 큐에 작업 등록
            ProcessOcrAnalysisJob::dispatch($requestId, $fileId, $file['path']);

            return [
                'success' => true,
                'message' => 'AI 분석 요청이 큐에 등록되었습니다.',
                'data' => [
                    'request_id' => $requestId,
                    'file_id' => $fileId,
                    'status' => 'pending',
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'AI 분석 요청 등록에 실패했습니다: ' . $e->getMessage(),
            ];
        }
    }

    private function getFileInfo(string $fileId): ?array
    {
        try {
            // OCR API에서 파일 정보 조회
            $response = Http::get(config('services.ocr.base_url') . '/requests/' . $fileId);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'id' => $data['request_id'] ?? $fileId,
                'name' => $data['original_filename'] ?? 'unknown',
                'type' => strtolower($data['file_type'] ?? 'unknown'),
                'path' => $data['stored_filename'] ?? '',
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get file info from OCR API: ' . $e->getMessage());
            return null;
        }
    }
}
