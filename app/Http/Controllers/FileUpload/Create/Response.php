<?php

namespace App\Http\Controllers\FileUpload\Create;

use Illuminate\Http\JsonResponse;

class Response extends JsonResponse
{
    public function __construct($data)
    {
        $response = [
            'success' => true,
            'message' => '파일이 성공적으로 업로드되었습니다',
            'data' => $data
        ];

        parent::__construct($response, 200);
    }
}