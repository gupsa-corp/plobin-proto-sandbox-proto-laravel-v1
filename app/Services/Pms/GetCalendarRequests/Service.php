<?php

namespace App\Services\Pms\GetCalendarRequests;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Service
{
    public function execute(array $params): array
    {
        $priority = $params['priority'] ?? '';
        $status = $params['status'] ?? '';
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $query = DB::table('plobin_analysis_requests as ar')
            ->leftJoin('plobin_users as requester', 'ar.requester_id', '=', 'requester.id')
            ->leftJoin('plobin_users as assignee', 'ar.assignee_id', '=', 'assignee.id')
            ->select(
                'ar.id',
                'ar.title',
                'ar.description',
                'ar.status',
                'ar.priority',
                'ar.start_date',
                'ar.end_date',
                'ar.estimated_hours',
                'ar.completed_percentage',
                'ar.created_at',
                'ar.completed_at',
                'requester.name as requester_name',
                'assignee.name as assignee_name'
            );

        // 필터 적용
        if ($priority) {
            $query->where('ar.priority', $priority);
        }

        if ($status) {
            $query->where('ar.status', $status);
        }

        // 날짜 범위 필터 (start_date 또는 end_date가 범위 내에 있으면 포함)
        if ($startDate && $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('ar.start_date', [$startDate, $endDate])
                  ->orWhereBetween('ar.end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('ar.start_date', '<=', $startDate)
                         ->where('ar.end_date', '>=', $endDate);
                  });
            });
        }

        // 취소되지 않은 요청만 조회
        $query->whereNull('ar.cancelled_at');

        $requests = $query->orderBy('ar.start_date', 'asc')
            ->orderBy('ar.created_at', 'desc')
            ->get();

        // Service 인스턴스 생성
        $typeService = new \App\Services\Pms\GetTypeByStatus\Service();
        $colorService = new \App\Services\Pms\GetColorByPriority\Service();

        // 캘린더 데이터로 변환
        $calendarData = $requests->map(function ($request) use ($typeService, $colorService) {
            return [
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'date' => $request->end_date ?? $request->start_date, // 캘린더 표시용 날짜 (종료일 우선)
                'status' => $request->status,
                'priority' => $request->priority,
                'requester' => $request->requester_name,
                'assignee' => $request->assignee_name,
                'estimated_hours' => $request->estimated_hours,
                'completed_percentage' => $request->completed_percentage,
                'type' => $typeService->execute($request->status),
                'color' => $colorService->execute($request->priority),
                'created_at' => $request->created_at,
                'completed_at' => $request->completed_at,
            ];
        })->toArray();

        return [
            'success' => true,
            'data' => $calendarData,
            'total' => count($calendarData),
        ];
    }
}
