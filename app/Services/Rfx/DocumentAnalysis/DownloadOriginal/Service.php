<?php

namespace App\Services\Rfx\DocumentAnalysis\DownloadOriginal;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Service
{
    public function execute(string $requestId, int $pageNumber = 1): StreamedResponse|array
    {
        try {
            // OCR API에서 원본 이미지 가져오기
            $url = config('services.ocr.base_url') . "/requests/{$requestId}/pages/{$pageNumber}/original";

            $response = Http::get($url);

            if (!$response->successful()) {
                Log::error("원본 이미지 다운로드 실패: {$requestId}, 페이지: {$pageNumber}");
                return [
                    'success' => false,
                    'message' => '원본 이미지를 찾을 수 없습니다.'
                ];
            }

            // 이미지 스트림 응답 생성
            $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
            $contentDisposition = $response->header('Content-Disposition') ?? "attachment; filename=\"page_{$pageNumber}.jpg\"";

            return response()->stream(function() use ($response) {
                echo $response->body();
            }, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => $contentDisposition,
            ]);

        } catch (\Exception $e) {
            Log::error("원본 이미지 다운로드 오류: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '이미지 다운로드 중 오류가 발생했습니다.'
            ];
        }
    }
}
