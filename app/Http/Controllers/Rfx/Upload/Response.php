<?php

namespace App\Http\Controllers\Rfx\Upload;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function success(array $data): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => '파일이 성공적으로 업로드되었습니다',
            'data' => $data
        ]);
    }

    public static function error(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], 422);
    }
}