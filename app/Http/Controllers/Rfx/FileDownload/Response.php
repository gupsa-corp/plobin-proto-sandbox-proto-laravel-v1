<?php

namespace App\Http\Controllers\Rfx\FileDownload;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function notFound(string $message = '파일을 찾을 수 없습니다'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null
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

    public static function unauthorized(string $message = '파일에 접근할 권한이 없습니다'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null
        ];
    }
}