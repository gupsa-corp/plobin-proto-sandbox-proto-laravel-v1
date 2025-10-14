<?php

namespace App\Http\Controllers\Rfx\ExternalImport\ImportData;

class Response
{
    public static function success(array $data): array
    {
        return [
            'success' => true,
            'message' => 'FastAPI 데이터 임포트가 시작되었습니다.',
            'data' => $data
        ];
    }

    public static function error(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null
        ];
    }
}
