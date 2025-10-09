<?php

namespace App\Http\Controllers\DocumentAnalysis\Create;

use Illuminate\Http\JsonResponse;

class Response extends JsonResponse
{
    public function __construct($data)
    {
        $response = [
            'success' => true,
            'message' => '문서 분석이 요청되었습니다',
            'data' => $data
        ];

        parent::__construct($response, 200);
    }
}