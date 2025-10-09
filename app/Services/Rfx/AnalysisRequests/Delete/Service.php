<?php

namespace App\Services\Rfx\AnalysisRequests\Delete;

use App\Models\Plobin\AnalysisRequest;

class Service
{
    public function execute($requestId): array
    {
        $request = AnalysisRequest::find($requestId);
        
        if (!$request) {
            return [
                'success' => false,
                'message' => '요청을 찾을 수 없습니다.'
            ];
        }

        $request->delete();

        return [
            'success' => true,
            'message' => '요청이 삭제되었습니다.'
        ];
    }
}