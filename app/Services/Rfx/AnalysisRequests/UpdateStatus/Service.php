<?php

namespace App\Services\Rfx\AnalysisRequests\UpdateStatus;

use App\Models\Plobin\AnalysisRequest;

class Service
{
    public function execute($requestId, $status): array
    {
        $request = AnalysisRequest::find($requestId);
        
        if (!$request) {
            return [
                'success' => false,
                'message' => '요청을 찾을 수 없습니다.'
            ];
        }

        $updateData = ['status' => $status];
        
        if ($status === 'completed') {
            $updateData['completed_at'] = now();
            $updateData['completed_percentage'] = 100;
        } elseif ($status === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        $request->update($updateData);

        return [
            'success' => true,
            'message' => '요청 상태가 업데이트되었습니다.'
        ];
    }
}