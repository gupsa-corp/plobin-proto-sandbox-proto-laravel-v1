<?php

namespace App\Services\Rfx\DocumentAnalysis\GetResult;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute($documentId): array
    {
        try {
            $response = Http::get(config('services.ocr.base_url') . "/requests/{$documentId}");

            if (!$response->successful()) {
                Log::error("OCR API 요청 상세 조회 실패: {$documentId}");
                return [
                    'summary' => '분석 결과를 불러올 수 없습니다.',
                    'keywords' => [],
                    'categories' => [],
                    'extractedData' => [],
                    'recommendations' => []
                ];
            }

            $data = $response->json();
            $pages = $data['pages'] ?? [];

            // 모든 페이지의 텍스트 블록 수집
            $allBlocks = [];
            foreach ($pages as $page) {
                if (isset($page['blocks'])) {
                    $allBlocks = array_merge($allBlocks, $page['blocks']);
                }
            }

            // 텍스트 추출
            $extractedTexts = array_map(function($block) {
                return $block['text'] ?? '';
            }, $allBlocks);

            // 신뢰도 계산
            $confidences = array_map(function($block) {
                return $block['confidence'] ?? 0;
            }, $allBlocks);
            $avgConfidence = count($confidences) > 0 ? array_sum($confidences) / count($confidences) : 0;

            return [
                'summary' => $this->generateSummary($extractedTexts),
                'keywords' => $this->extractKeywords($extractedTexts),
                'categories' => $this->classifyDocument($data),
                'extractedData' => $this->extractStructuredData($allBlocks),
                'recommendations' => []
            ];

        } catch (\Exception $e) {
            Log::error("OCR API 요청 상세 조회 실패: " . $e->getMessage());
            return [
                'summary' => '분석 결과를 불러오는 중 오류가 발생했습니다.',
                'keywords' => [],
                'categories' => [],
                'extractedData' => [],
                'recommendations' => []
            ];
        }
    }

    private function generateSummary(array $texts): string
    {
        $allText = implode(' ', $texts);
        $words = explode(' ', $allText);
        $wordCount = count($words);
        $blockCount = count($texts);

        return "총 {$blockCount}개의 텍스트 블록에서 {$wordCount}개의 단어가 추출되었습니다.";
    }

    private function extractKeywords(array $texts): array
    {
        // 간단한 키워드 추출 (실제로는 더 정교한 알고리즘 필요)
        $allText = implode(' ', $texts);
        $words = preg_split('/\s+/', $allText);
        $words = array_filter($words, function($word) {
            return mb_strlen($word) > 2;
        });

        $wordCounts = array_count_values($words);
        arsort($wordCounts);

        return array_slice(array_keys($wordCounts), 0, 10);
    }

    private function classifyDocument(array $data): array
    {
        $fileType = $data['file_type'] ?? 'unknown';
        $categories = [];

        if (in_array(strtolower($fileType), ['pdf', 'image'])) {
            $categories[] = strtoupper($fileType) . ' 문서';
        }

        $pageCount = $data['total_pages'] ?? 0;
        if ($pageCount > 10) {
            $categories[] = '다중 페이지';
        } elseif ($pageCount > 0) {
            $categories[] = '단일 페이지';
        }

        return $categories;
    }

    private function extractStructuredData(array $blocks): array
    {
        $data = [];

        // 텍스트 블록 수
        $data['총 블록 수'] = count($blocks);

        // 평균 신뢰도
        $confidences = array_map(function($block) {
            return $block['confidence'] ?? 0;
        }, $blocks);
        if (count($confidences) > 0) {
            $data['평균 신뢰도'] = round(array_sum($confidences) / count($confidences) * 100, 1) . '%';
        }

        return $data;
    }
}