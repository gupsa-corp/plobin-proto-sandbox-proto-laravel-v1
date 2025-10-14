<?php

namespace App\Services\Rfx\ExternalImport\FetchFromFastApi;

use Illuminate\Support\Facades\Http;
use App\Exceptions\Rfx\ExternalImport\FastApiConnectionFailed\Exception as FastApiConnectionFailedException;

class Service
{
    private const FASTAPI_BASE_URL = 'http://localhost:6003';

    public function execute(string $requestId): array
    {
        $url = self::FASTAPI_BASE_URL . '/export/' . $requestId;

        $response = Http::timeout(60)->get($url);

        if (!$response->successful()) {
            throw new FastApiConnectionFailedException(
                "FastAPI 연결 실패: HTTP {$response->status()} - {$response->body()}"
            );
        }

        return $response->json();
    }
}
