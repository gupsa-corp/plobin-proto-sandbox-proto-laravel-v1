<?php

namespace App\Http\Controllers\Rfx\FileUpload;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function success(array $data): array
    {
        return [
            'success' => true,
            'message' => '파일이 성공적으로 업로드되었습니다',
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

    public static function validationError(string $message = '파일 업로드에 실패했습니다'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => null
        ];
    }
}