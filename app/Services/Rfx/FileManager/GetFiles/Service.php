<?php

namespace App\Services\Rfx\FileManager\GetFiles;

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
                Log::error('OCR API 파일 목록 조회 실패');
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
                    '업로드됨' => 'pending',
                    '분석중' => 'processing',
                    '완료' => 'completed',
                    '오류' => 'failed'
                ];
                $apiStatus = $statusMap[$filters['status']] ?? $filters['status'];
                $requests = array_filter($requests, function($request) use ($apiStatus) {
                    return ($request['status'] ?? '') === $apiStatus;
                });
            }

            // 타입 필터 적용
            if (!empty($filters['type'])) {
                $requests = array_filter($requests, function($request) use ($filters) {
                    return str_contains(strtolower($request['file_type'] ?? ''), strtolower($filters['type']));
                });
            }

            // 정렬 적용
            $sortBy = $filters['sortBy'] ?? 'uploadedAt';
            $sortDirection = $filters['sortDirection'] ?? 'desc';

            usort($requests, function($a, $b) use ($sortBy, $sortDirection) {
                $aValue = match($sortBy) {
                    'name' => $a['original_filename'] ?? '',
                    'uploadedAt' => $a['created_at'] ?? '',
                    default => $a['created_at'] ?? ''
                };
                $bValue = match($sortBy) {
                    'name' => $b['original_filename'] ?? '',
                    'uploadedAt' => $b['created_at'] ?? '',
                    default => $b['created_at'] ?? ''
                };

                $result = $aValue <=> $bValue;
                return $sortDirection === 'desc' ? -$result : $result;
            });

            return array_map(function($request) {
                return [
                    'id' => $request['request_id'],
                    'name' => $request['original_filename'],
                    'originalName' => $request['original_filename'],
                    'size' => isset($request['file_size']) ? $this->formatFileSize($request['file_size']) : '-',
                    'type' => strtoupper($request['file_type'] ?? 'unknown'),
                    'status' => $this->mapStatus($request['status'] ?? 'pending'),
                    'uploadedAt' => isset($request['created_at']) ? date('Y-m-d H:i:s', strtotime($request['created_at'])) : '-',
                    'analyzedAt' => isset($request['completed_at']) ? date('Y-m-d H:i:s', strtotime($request['completed_at'])) : null,
                    'tags' => [],
                    'summary' => null,
                    'downloadCount' => 0
                ];
            }, $requests);

        } catch (\Exception $e) {
            Log::error('OCR API 파일 목록 조회 실패: ' . $e->getMessage());
            return [];
        }
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'pending' => '업로드됨',
            'processing' => '분석중',
            'completed' => '완료',
            'failed' => '오류',
            default => '업로드됨'
        };
    }

    private function getFileExtension($mimeType): string
    {
        return match($mimeType) {
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            default => 'unknown'
        };
    }
}