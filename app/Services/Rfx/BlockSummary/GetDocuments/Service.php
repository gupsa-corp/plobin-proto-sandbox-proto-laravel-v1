<?php

namespace App\Services\Rfx\BlockSummary\GetDocuments;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(array $filters): array
    {
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');

        try {
            $response = Http::timeout(30)->get("{$ocrBaseUrl}/requests");

            if (!$response->successful()) {
                return [];
            }

            $requests = $response->json();
            $documents = [];

            // requests가 배열인지 확인
            $requestList = $requests['requests'] ?? $requests;

            foreach ($requestList as $request) {
                // 필터 적용
                $filename = $request['original_filename'] ?? $request['filename'] ?? 'Unknown';
                if (!empty($filters['search'])) {
                    if (stripos($filename, $filters['search']) === false) {
                        continue;
                    }
                }

                if (!empty($filters['status'])) {
                    $status = $this->getStatusText($request['status'] ?? '');
                    if ($status !== $filters['status']) {
                        continue;
                    }
                }

                $documents[] = [
                    'id' => $request['request_id'],
                    'fileName' => $filename,
                    'documentType' => strtoupper($request['file_type'] ?? 'Unknown'),
                    'status' => $this->getStatusText($request['status'] ?? ''),
                    'pageCount' => $request['total_pages'] ?? 0,
                    'analyzedAt' => isset($request['completed_at']) ? date('Y-m-d H:i', strtotime($request['completed_at'])) : (isset($request['created_at']) ? date('Y-m-d H:i', strtotime($request['created_at'])) : null),
                ];
            }

            return $documents;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getStatusText($status): string
    {
        return match ($status) {
            'completed' => '완료',
            'processing' => '분석중',
            'pending' => '대기',
            'failed' => '오류',
            default => '알 수 없음',
        };
    }
}
