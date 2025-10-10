<?php

namespace App\Services\Rfx\AnalysisRequests\List;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(array $filters = []): array
    {
        try {
            $response = Http::get(config('services.ocr.base_url') . '/requests', [
                'page' => 1,
                'limit' => 100
            ]);

            if (!$response->successful()) {
                Log::error('OCR API 분석 요청 목록 조회 실패');
                return [];
            }

            $requests = $response->json()['requests'] ?? [];

            // 상태 필터 적용
            if (!empty($filters['status'])) {
                $statusMap = [
                    '전체' => '',
                    '대기중' => 'pending',
                    '진행중' => 'processing',
                    '완료' => 'completed',
                    '취소됨' => 'failed'
                ];
                $apiStatus = $statusMap[$filters['status']] ?? $filters['status'];
                if ($apiStatus !== '') {
                    $requests = array_filter($requests, function($request) use ($apiStatus) {
                        return ($request['status'] ?? '') === $apiStatus;
                    });
                }
            }

            // 우선순위 필터 (OCR API에는 우선순위가 없으므로 파일 크기로 대체)
            if (!empty($filters['priority']) && $filters['priority'] !== '전체') {
                $priorityMap = [
                    '높음' => 'high',
                    '보통' => 'medium',
                    '낮음' => 'low'
                ];
                $priority = $priorityMap[$filters['priority']] ?? $filters['priority'];

                // 파일 크기 기반 우선순위 매핑
                $requests = array_filter($requests, function($request) use ($priority) {
                    $fileSize = $request['file_size'] ?? 0;
                    $calculatedPriority = 'medium';

                    if ($fileSize > 5000000) { // 5MB 이상
                        $calculatedPriority = 'high';
                    } elseif ($fileSize < 1000000) { // 1MB 미만
                        $calculatedPriority = 'low';
                    }

                    return $calculatedPriority === $priority;
                });
            }

            // 날짜 필터 적용
            if (!empty($filters['date'])) {
                $requests = array_filter($requests, function($request) use ($filters) {
                    $createdDate = isset($request['created_at']) ? date('Y-m-d', strtotime($request['created_at'])) : null;
                    return $createdDate === $filters['date'];
                });
            }

            return array_map(function($request) {
                $fileSize = $request['file_size'] ?? 0;
                $priority = 'medium';
                if ($fileSize > 5000000) {
                    $priority = 'high';
                } elseif ($fileSize < 1000000) {
                    $priority = 'low';
                }

                $completedPercentage = 0;
                if (($request['status'] ?? '') === 'completed') {
                    $completedPercentage = 100;
                } elseif (($request['status'] ?? '') === 'processing') {
                    $completedPercentage = 50;
                }

                return [
                    'id' => $request['request_id'],
                    'title' => $request['original_filename'],
                    'description' => '파일 타입: ' . strtoupper($request['file_type'] ?? 'unknown') . ', 페이지 수: ' . ($request['total_pages'] ?? 0),
                    'status' => $this->mapStatus($request['status'] ?? 'pending'),
                    'priority' => $this->mapPriority($priority),
                    'requester' => '시스템',
                    'assignee' => null,
                    'createdAt' => isset($request['created_at']) ? date('Y-m-d H:i:s', strtotime($request['created_at'])) : '-',
                    'requiredBy' => null,
                    'documentCount' => 1,
                    'estimatedHours' => ($request['total_pages'] ?? 0) * 0.5,
                    'completedPercentage' => $completedPercentage,
                    'completedAt' => isset($request['completed_at']) ? date('Y-m-d H:i:s', strtotime($request['completed_at'])) : null,
                    'cancelledAt' => ($request['status'] ?? '') === 'failed' && isset($request['updated_at']) ? date('Y-m-d H:i:s', strtotime($request['updated_at'])) : null,
                    'cancelReason' => ($request['status'] ?? '') === 'failed' ? '처리 실패' : null
                ];
            }, $requests);

        } catch (\Exception $e) {
            Log::error('OCR API 분석 요청 목록 조회 실패: ' . $e->getMessage());
            return [];
        }
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'pending' => '대기중',
            'processing' => '진행중',
            'completed' => '완료',
            'failed' => '취소됨',
            default => '대기중'
        };
    }

    private function mapPriority(string $priority): string
    {
        return match($priority) {
            'high' => '높음',
            'medium' => '보통',
            'low' => '낮음',
            default => '보통'
        };
    }
}