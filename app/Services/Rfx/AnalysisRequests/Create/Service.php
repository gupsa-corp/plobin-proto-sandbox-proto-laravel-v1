<?php

namespace App\Services\Rfx\AnalysisRequests\Create;

use App\Models\Plobin\AnalysisRequest;

class Service
{
    public function execute(array $data): array
    {
        try {
            $request = AnalysisRequest::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'required_by' => $data['requiredBy'] ?? null,
                'estimated_hours' => $data['estimatedHours'] ?? null,
                'requester_id' => $data['requesterId'] ?? 1, // 임시로 1번 사용자
                'status' => 'pending'
            ]);

            // 파일 연결 (documentIds가 있는 경우)
            if (!empty($data['documentIds'])) {
                $request->files()->attach($data['documentIds']);
            }

            return [
                'success' => true,
                'data' => [
                    'id' => $request->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'priority' => $request->priority,
                    'requiredBy' => $request->required_by?->format('Y-m-d'),
                    'status' => $request->status,
                    'createdAt' => $request->created_at->format('Y-m-d H:i:s')
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '요청 생성에 실패했습니다: ' . $e->getMessage()
            ];
        }
    }
}