<?php

namespace App\Services\Rfx\AnalysisRequests\List;

use App\Models\Plobin\AnalysisRequest;

class Service
{
    public function execute(array $filters = []): array
    {
        $query = AnalysisRequest::with(['requester', 'assignee', 'files']);

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 우선순위 필터 적용
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // 날짜 필터 적용
        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return $requests->map(function($request) {
            return [
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'priority' => $request->priority,
                'requester' => $request->requester->name,
                'assignee' => $request->assignee?->name,
                'createdAt' => $request->created_at->format('Y-m-d H:i:s'),
                'requiredBy' => $request->required_by?->format('Y-m-d'),
                'documentCount' => $request->files->count(),
                'estimatedHours' => $request->estimated_hours,
                'completedPercentage' => $request->completed_percentage,
                'completedAt' => $request->completed_at?->format('Y-m-d H:i:s'),
                'cancelledAt' => $request->cancelled_at?->format('Y-m-d H:i:s'),
                'cancelReason' => $request->cancel_reason
            ];
        })->toArray();
    }
}