<?php

namespace App\Services\Rfx\AnalysisRequests\UpdatePriority;

use App\Models\Plobin\AnalysisRequest;

class Service
{
    public function execute($requestId, $priority): array
    {
        $request = AnalysisRequest::find($requestId);
        
        if (!$request) {
            return [
                'success' => false,
                'message' => '요청을 찾을 수 없습니다.'
            ];
        }

        $request->update(['priority' => $priority]);

        return [
            'success' => true,
            'message' => '요청 우선순위가 업데이트되었습니다.'
        ];
    }
}