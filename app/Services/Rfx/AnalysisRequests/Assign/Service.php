<?php

namespace App\Services\Rfx\AnalysisRequests\Assign;

use App\Models\Plobin\AnalysisRequest;
use App\Models\Plobin\PlobinUser;

class Service
{
    public function execute($requestId, $assigneeId): array
    {
        $request = AnalysisRequest::find($requestId);
        
        if (!$request) {
            return [
                'success' => false,
                'message' => '요청을 찾을 수 없습니다.'
            ];
        }

        $assignee = PlobinUser::find($assigneeId);
        
        if (!$assignee) {
            return [
                'success' => false,
                'message' => '담당자를 찾을 수 없습니다.'
            ];
        }

        $request->update(['assignee_id' => $assigneeId]);

        return [
            'success' => true,
            'message' => '요청이 담당자에게 배정되었습니다.'
        ];
    }
}