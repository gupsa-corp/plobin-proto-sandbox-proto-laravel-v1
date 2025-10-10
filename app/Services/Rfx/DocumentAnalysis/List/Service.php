<?php

namespace App\Services\Rfx\DocumentAnalysis\List;

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
                Log::error('OCR API 문서 목록 조회 실패');
                return [];
            }

            $requests = $response->json()['requests'] ?? [];

            // 검색 필터 적용
            if (!empty($filters['search'])) {
                $search = strtolower($filters['search']);
                $requests = array_filter($requests, function($request) use ($search) {
                    return str_contains(strtolower($request['original_filename'] ?? ''), $search);
                });
            }

            // 상태 필터 적용
            if (!empty($filters['status'])) {
                $statusMap = [
                    '완료' => 'completed',
                    '분석중' => 'processing',
                    '대기' => 'pending',
                    '오류' => 'failed'
                ];
                $apiStatus = $statusMap[$filters['status']] ?? $filters['status'];
                $requests = array_filter($requests, function($request) use ($apiStatus) {
                    return ($request['status'] ?? '') === $apiStatus;
                });
            }

            // 날짜 필터 적용
            if (!empty($filters['date'])) {
                $requests = array_filter($requests, function($request) use ($filters) {
                    $completedDate = isset($request['completed_at']) ? date('Y-m-d', strtotime($request['completed_at'])) : null;
                    return $completedDate === $filters['date'];
                });
            }

            return array_map(function($request) {
                return [
                    'id' => $request['request_id'],
                    'fileName' => $request['original_filename'],
                    'status' => $this->mapStatus($request['status'] ?? 'pending'),
                    'analyzedAt' => isset($request['completed_at']) ? date('Y-m-d H:i:s', strtotime($request['completed_at'])) : null,
                    'confidence' => isset($request['pages'][0]['average_confidence']) ? round($request['pages'][0]['average_confidence'] * 100, 1) : null,
                    'documentType' => strtoupper($request['file_type'] ?? 'unknown'),
                    'keywordCount' => $request['total_blocks'] ?? 0,
                    'pageCount' => $request['total_pages'] ?? 0
                ];
            }, $requests);

        } catch (\Exception $e) {
            Log::error('OCR API 문서 목록 조회 실패: ' . $e->getMessage());
            return [];
        }
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'pending' => '대기',
            'processing' => '분석중',
            'completed' => '완료',
            'failed' => '오류',
            default => '대기'
        };
    }
}