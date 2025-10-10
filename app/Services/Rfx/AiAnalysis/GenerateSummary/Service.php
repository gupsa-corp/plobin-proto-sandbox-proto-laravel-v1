<?php

namespace App\Services\Rfx\AiAnalysis\GenerateSummary;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(int $requestId): array
    {
        try {
            // 1. AI 분석 요청 조회
            $request = \DB::table('rfx_ai_analysis_requests')
                ->where('id', $requestId)
                ->first();

            if (!$request) {
                return [
                    'success' => false,
                    'message' => '분석 요청을 찾을 수 없습니다.',
                ];
            }

            if ($request->status !== 'completed') {
                return [
                    'success' => false,
                    'message' => '완료된 분석만 요약할 수 있습니다.',
                ];
            }

            // 2. OCR 결과 파싱
            $ocrResult = json_decode($request->result, true);

            if (!$ocrResult) {
                return [
                    'success' => false,
                    'message' => 'OCR 결과를 파싱할 수 없습니다.',
                ];
            }

            // 3. 요약 생성
            $summary = $this->generateSummary($ocrResult);

            return [
                'success' => true,
                'data' => $summary,
            ];

        } catch (\Exception $e) {
            Log::error('AI 분석 요약 생성 실패: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => '요약 생성에 실패했습니다: ' . $e->getMessage(),
            ];
        }
    }

    private function generateSummary(array $ocrResult): array
    {
        $summary = [
            'document_info' => [
                'filename' => $ocrResult['original_filename'] ?? 'Unknown',
                'file_type' => strtoupper($ocrResult['file_type'] ?? 'Unknown'),
                'file_size' => $this->formatFileSize($ocrResult['file_size'] ?? 0),
                'total_pages' => $ocrResult['total_pages'] ?? 0,
            ],
            'processing_info' => [
                'status' => $this->translateStatus($ocrResult['status'] ?? 'unknown'),
                'created_at' => $ocrResult['created_at'] ?? null,
                'completed_at' => $ocrResult['completed_at'] ?? null,
                'processing_time' => $this->calculateProcessingTime($ocrResult),
            ],
            'quality_metrics' => [
                'average_confidence' => 0,
                'total_blocks' => 0,
                'pages_processed' => 0,
            ],
            'page_details' => [],
        ];

        // 페이지별 정보 처리
        if (isset($ocrResult['pages']) && is_array($ocrResult['pages'])) {
            $totalConfidence = 0;
            $totalBlocks = 0;
            $pageCount = count($ocrResult['pages']);

            foreach ($ocrResult['pages'] as $page) {
                $pageConfidence = $page['average_confidence'] ?? 0;
                $pageBlocks = $page['total_blocks'] ?? 0;

                $totalConfidence += $pageConfidence;
                $totalBlocks += $pageBlocks;

                $summary['page_details'][] = [
                    'page_number' => $page['page_number'] ?? 0,
                    'blocks' => $pageBlocks,
                    'confidence' => $this->formatPercentage($pageConfidence),
                    'processing_time' => $this->formatTime($page['processing_time'] ?? 0),
                    'quality_grade' => $this->getQualityGrade($pageConfidence),
                ];
            }

            $summary['quality_metrics']['average_confidence'] = $pageCount > 0
                ? $this->formatPercentage($totalConfidence / $pageCount)
                : '0%';
            $summary['quality_metrics']['total_blocks'] = $totalBlocks;
            $summary['quality_metrics']['pages_processed'] = $pageCount;
        }

        return $summary;
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

    private function translateStatus(string $status): string
    {
        return match($status) {
            'pending' => '대기중',
            'processing' => '처리중',
            'completed' => '완료',
            'failed' => '실패',
            default => '알 수 없음',
        };
    }

    private function calculateProcessingTime(array $ocrResult): string
    {
        if (isset($ocrResult['created_at']) && isset($ocrResult['completed_at'])) {
            try {
                $start = new \DateTime($ocrResult['created_at']);
                $end = new \DateTime($ocrResult['completed_at']);
                $diff = $start->diff($end);

                if ($diff->h > 0) {
                    return sprintf('%d시간 %d분 %d초', $diff->h, $diff->i, $diff->s);
                } elseif ($diff->i > 0) {
                    return sprintf('%d분 %d초', $diff->i, $diff->s);
                } else {
                    return sprintf('%d초', $diff->s);
                }
            } catch (\Exception $e) {
                return '계산 불가';
            }
        }
        return '알 수 없음';
    }

    private function formatPercentage(float $value): string
    {
        return round($value * 100, 2) . '%';
    }

    private function formatTime(float $seconds): string
    {
        if ($seconds < 1) {
            return round($seconds * 1000) . 'ms';
        } elseif ($seconds < 60) {
            return round($seconds, 2) . '초';
        } else {
            $minutes = floor($seconds / 60);
            $secs = round($seconds % 60, 2);
            return "{$minutes}분 {$secs}초";
        }
    }

    private function getQualityGrade(float $confidence): array
    {
        if ($confidence >= 0.9) {
            return ['grade' => '매우 높음', 'color' => 'green'];
        } elseif ($confidence >= 0.7) {
            return ['grade' => '높음', 'color' => 'blue'];
        } elseif ($confidence >= 0.5) {
            return ['grade' => '보통', 'color' => 'yellow'];
        } else {
            return ['grade' => '낮음', 'color' => 'red'];
        }
    }
}
