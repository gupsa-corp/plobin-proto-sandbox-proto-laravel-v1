<?php

namespace App\Services\Rfx\DocumentAnalysis\Export;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function execute($documentId, $format): array
    {
        try {
            // OCR API에서 요청 상세 정보 가져오기
            $response = Http::get(config('services.ocr.base_url') . "/requests/{$documentId}");

            if (!$response->successful()) {
                Log::error("OCR API 요청 조회 실패: {$documentId}");
                return [
                    'success' => false,
                    'message' => '문서를 찾을 수 없습니다.'
                ];
            }

            $data = $response->json();

            switch ($format) {
                case 'json':
                    return $this->exportJson($documentId, $data);

                case 'pdf':
                case 'excel':
                    return [
                        'success' => false,
                        'message' => "{$format} 형식은 아직 지원하지 않습니다."
                    ];

                default:
                    return [
                        'success' => false,
                        'message' => '지원하지 않는 형식입니다.'
                    ];
            }

        } catch (\Exception $e) {
            Log::error("내보내기 실패: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '내보내기 중 오류가 발생했습니다.'
            ];
        }
    }

    private function exportJson($documentId, $data): array
    {
        try {
            $filename = "analysis_{$documentId}_" . date('YmdHis') . ".json";
            $filepath = "exports/{$filename}";

            // JSON 파일 생성
            Storage::disk('public')->put($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return [
                'success' => true,
                'message' => 'JSON 파일로 내보내기 완료',
                'downloadUrl' => Storage::disk('public')->url($filepath),
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            Log::error("JSON 내보내기 실패: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'JSON 파일 생성 중 오류가 발생했습니다.'
            ];
        }
    }
}