<?php

namespace App\Services\Rfx\DocumentAnalysis\GetResult;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Service
{
    public function execute($documentId): array
    {
        try {
            // 1단계: DB에서 임포트된 데이터 우선 확인
            $importedData = DB::table('rfx_external_imports')
                ->where('request_id', $documentId)
                ->first();

            if ($importedData && $importedData->status === 'completed') {
                // DB에서 가져온 데이터를 FastAPI 형식으로 변환
                return $this->buildFromDatabase($documentId, $importedData);
            }

            // 2단계: DB에 없으면 FastAPI에서 조회
            $response = Http::get(config('services.ocr.base_url') . "/requests/{$documentId}");

            if (!$response->successful()) {
                Log::error("OCR API 요청 상세 조회 실패: {$documentId}");
                throw new \Exception("문서를 찾을 수 없습니다. Request ID: {$documentId}");
            }

            $data = $response->json();
            $pages = $data['pages'] ?? [];

            // 2단계: 각 페이지별 블록 데이터 조회
            $pagesWithBlocks = [];
            $allBlocks = [];

            foreach ($pages as $page) {
                $pageNumber = $page['page_number'];

                try {
                    // 페이지별 상세 데이터 조회 (블록 포함)
                    $pageResponse = Http::get(config('services.ocr.base_url') . "/requests/{$documentId}/pages/{$pageNumber}");

                    if ($pageResponse->successful()) {
                        $pageData = $pageResponse->json();

                        // 블록 데이터에 이미지 URL 추가
                        $blocksWithImages = [];
                        if (isset($pageData['blocks'])) {
                            foreach ($pageData['blocks'] as $blockIndex => $block) {
                                $blockId = $block['block_id'] ?? $blockIndex;
                                $block['image_url'] = config('services.ocr.base_url') . "/requests/{$documentId}/pages/{$pageNumber}/blocks/{$blockId}/image";
                                $blocksWithImages[] = $block;
                                $allBlocks[] = $block;
                            }
                        }

                        // 페이지 정보에 블록 추가 (이미지 URL 포함)
                        $pagesWithBlocks[] = [
                            'page_number' => $pageNumber,
                            'total_blocks' => $page['total_blocks'] ?? count($blocksWithImages),
                            'average_confidence' => $page['average_confidence'] ?? 0,
                            'processing_time' => $page['processing_time'] ?? 0,
                            'visualization_url' => config('services.ocr.base_url') . "/requests/{$documentId}/pages/{$pageNumber}/visualization",
                            'blocks' => $blocksWithImages
                        ];
                    } else {
                        // 페이지 상세 조회 실패 시 기본 페이지 정보만 사용
                        $pagesWithBlocks[] = $page;
                    }
                } catch (\Exception $e) {
                    Log::warning("페이지 {$pageNumber} 블록 조회 실패: " . $e->getMessage());
                    $pagesWithBlocks[] = $page;
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

            // OCR 원본 데이터에 블록 정보 추가
            $data['pages'] = $pagesWithBlocks;

            return [
                'summary' => $this->generateSummary($extractedTexts),
                'keywords' => $this->extractKeywords($extractedTexts),
                'categories' => $this->classifyDocument($data),
                'extractedData' => $this->extractStructuredData($allBlocks),
                'recommendations' => [],
                'ocrRawData' => $data  // OCR API 원본 응답 (블록 포함)
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

    private function buildFromDatabase(string $documentId, $importedData): array
    {
        // metadata와 summary 파싱
        $metadata = json_decode($importedData->metadata, true);
        $summary = json_decode($importedData->summary, true);

        // DB에서 document_assets 조회
        $assets = DB::table('rfx_document_assets')
            ->where('analysis_request_id', $documentId)
            ->orderBy('page_number')
            ->orderBy('display_order')
            ->get();

        // 페이지별로 블록 그룹핑
        $pageGroups = $assets->groupBy('page_number');
        $pagesWithBlocks = [];
        $allBlocks = [];

        foreach ($pageGroups as $pageNumber => $blocks) {
            $blocksArray = [];
            $confidences = [];

            foreach ($blocks as $block) {
                $blockData = [
                    'text' => $block->content,
                    'confidence' => $block->confidence,
                    'bbox' => $block->bbox ? json_decode($block->bbox, true) : [],
                    'block_type' => $block->asset_type,
                    'block_id' => $block->asset_id,
                    'parent_id' => null,
                    'children' => [],
                    'level' => 0,
                    'image_url' => null,
                    'bbox_x' => $block->bbox_x,
                    'bbox_y' => $block->bbox_y,
                    'bbox_width' => $block->bbox_width,
                    'bbox_height' => $block->bbox_height
                ];

                $blocksArray[] = $blockData;
                $allBlocks[] = $blockData;
                $confidences[] = $block->confidence;
            }

            $avgConfidence = count($confidences) > 0 ? array_sum($confidences) / count($confidences) : 0;

            $pagesWithBlocks[] = [
                'page_number' => $pageNumber,
                'total_blocks' => count($blocksArray),
                'average_confidence' => $avgConfidence,
                'processing_time' => 0,
                'visualization_url' => null,
                'blocks' => $blocksArray
            ];
        }

        // 텍스트 추출
        $extractedTexts = array_map(function($block) {
            return $block['text'] ?? '';
        }, $allBlocks);

        // OCR 원본 데이터 구성 (FastAPI 형식)
        $ocrRawData = [
            'request_id' => $documentId,
            'original_filename' => $metadata['original_filename'] ?? $importedData->original_filename,
            'file_type' => $metadata['file_type'] ?? 'pdf',
            'file_size' => $metadata['file_size'] ?? 0,
            'status' => 'completed',
            'created_at' => $metadata['created_at'] ?? null,
            'completed_at' => $metadata['completed_at'] ?? $importedData->imported_at,
            'total_pages' => $importedData->total_pages,
            'total_processing_time' => $summary['total_processing_time'] ?? null,
            'pages' => $pagesWithBlocks
        ];

        return [
            'summary' => $this->generateSummary($extractedTexts),
            'keywords' => $this->extractKeywords($extractedTexts),
            'categories' => $this->classifyDocument($ocrRawData),
            'extractedData' => $this->extractStructuredData($allBlocks),
            'recommendations' => [],
            'ocrRawData' => $ocrRawData
        ];
    }
}