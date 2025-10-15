<?php

namespace App\Services\Rfx\ExternalImport\FetchFromFastApi;

use Illuminate\Support\Facades\Http;
use App\Exceptions\Rfx\ExternalImport\FastApiConnectionFailed\Exception as FastApiConnectionFailedException;

class Service
{
    public function execute(string $requestId): array
    {
        // OCR API URL 환경변수 검증
        $ocrBaseUrl = env('OCR_API_BASE_URL');
        if (!$ocrBaseUrl) {
            throw new \RuntimeException('OCR_API_BASE_URL 환경변수가 설정되지 않았습니다.');
        }

        $url = $ocrBaseUrl . '/export/' . $requestId;

        $response = Http::timeout(60)->get($url);

        if (!$response->successful()) {
            throw new FastApiConnectionFailedException(
                "FastAPI 연결 실패: HTTP {$response->status()} - {$response->body()}"
            );
        }

        return $response->json();
    }
}
