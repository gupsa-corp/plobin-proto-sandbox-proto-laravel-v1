<?php

namespace App\Services\Rfx\DocumentSections\GetList;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(string $documentId, int $page = 1): array
    {
        // OCR-Reader API 호출하여 모든 블록 가져오기
        $ocrBaseUrl = config('services.ocr.base_url', 'http://127.0.0.1:8000');
        $requestId = $documentId; // documentId를 requestId로 직접 사용

        try {
            $response = Http::timeout(30)
                ->get("{$ocrBaseUrl}/requests/{$requestId}/pages/{$page}/blocks", [
                    'limit' => 1000 // 전체 블록 조회
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'OCR API 호출에 실패했습니다',
                    'data' => null
                ];
            }

            $data = $response->json();
            $blocks = $data['blocks'] ?? [];

            // 블록을 섹션으로 그룹화
            $sections = $this->groupBlocksIntoSections($blocks);

            // 문서 이름 조회 (request 메타데이터에서)
            $metaResponse = Http::timeout(30)->get("{$ocrBaseUrl}/requests/{$requestId}");
            $documentName = 'Unknown';
            if ($metaResponse->successful()) {
                $metaData = $metaResponse->json();
                $documentName = $metaData['filename'] ?? 'Unknown';
            }

            return [
                'success' => true,
                'message' => '섹션 목록을 성공적으로 조회했습니다',
                'data' => [
                    'document_id' => $documentId,
                    'document_name' => $documentName,
                    'page_number' => $page,
                    'sections' => $sections,
                    'statistics' => [
                        'total_sections' => count($sections),
                        'total_subsections' => $this->countSubsections($sections),
                        'total_blocks' => count($blocks)
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'OCR API 호출 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    private function groupBlocksIntoSections(array $blocks): array
    {
        $sections = [];
        $currentSection = null;
        $sectionCounter = 0;

        foreach ($blocks as $block) {
            $blockType = $block['block_type'] ?? 'other';
            $text = $block['text'] ?? '';

            // title 블록을 섹션 헤더로 인식
            if ($blockType === 'title' || $this->isSectionHeader($text)) {
                // 이전 섹션 저장
                if ($currentSection !== null) {
                    $sections[] = $currentSection;
                }

                // 새 섹션 시작
                $sectionCounter++;
                $sectionNumber = $this->extractSectionNumber($text) ?: (string) $sectionCounter;

                $currentSection = [
                    'section_id' => $sectionNumber,
                    'section_number' => $sectionNumber,
                    'title' => $this->cleanSectionTitle($text),
                    'block_count' => 1,
                    'block_ids' => [$block['block_id'] ?? 0],
                    'blocks' => [$block],
                    'confidence_average' => $block['confidence'] ?? 0,
                    'subsections' => []
                ];
            } else {
                // 현재 섹션에 블록 추가
                if ($currentSection !== null) {
                    $currentSection['block_count']++;
                    $currentSection['block_ids'][] = $block['block_id'] ?? 0;
                    $currentSection['blocks'][] = $block;

                    // 평균 신뢰도 재계산
                    $totalConfidence = $currentSection['confidence_average'] * ($currentSection['block_count'] - 1);
                    $totalConfidence += $block['confidence'] ?? 0;
                    $currentSection['confidence_average'] = $totalConfidence / $currentSection['block_count'];
                } else {
                    // 섹션 없이 시작된 블록들은 첫 번째 섹션으로
                    $sectionCounter++;
                    $currentSection = [
                        'section_id' => (string) $sectionCounter,
                        'section_number' => (string) $sectionCounter,
                        'title' => '기타',
                        'block_count' => 1,
                        'block_ids' => [$block['block_id'] ?? 0],
                        'blocks' => [$block],
                        'confidence_average' => $block['confidence'] ?? 0,
                        'subsections' => []
                    ];
                }
            }
        }

        // 마지막 섹션 저장
        if ($currentSection !== null) {
            $sections[] = $currentSection;
        }

        // 계층 구조 생성
        return $this->buildHierarchy($sections);
    }

    private function isSectionHeader(string $text): bool
    {
        // 섹션 헤더 패턴 감지
        $patterns = [
            '/^\d+\.\s/',           // "1. ", "2. "
            '/^\d+\.\d+\s/',        // "1.1 ", "2.3 "
            '/^[가-힣]\.\s/',       // "가. ", "나. "
            '/^\d+\)\s/',           // "1) ", "2) "
            '/^[IVX]+\.\s/',        // "I. ", "II. "
            '/^[A-Z]\.\s/',         // "A. ", "B. "
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function extractSectionNumber(string $text): ?string
    {
        // 섹션 번호 추출
        if (preg_match('/^(\d+(?:\.\d+)*)\s/', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([가-힣])\.\s/', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^(\d+)\)\s/', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([IVX]+)\.\s/', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([A-Z])\.\s/', $text, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function cleanSectionTitle(string $text): string
    {
        // 섹션 번호 제거하고 제목만 반환
        $cleaned = preg_replace('/^\d+(?:\.\d+)*\s+/', '', $text);
        $cleaned = preg_replace('/^[가-힣]\.\s+/', '', $cleaned);
        $cleaned = preg_replace('/^\d+\)\s+/', '', $cleaned);
        $cleaned = preg_replace('/^[IVX]+\.\s+/', '', $cleaned);
        $cleaned = preg_replace('/^[A-Z]\.\s+/', '', $cleaned);

        return trim($cleaned);
    }

    private function buildHierarchy(array $sections): array
    {
        $hierarchy = [];
        $parentMap = [];

        foreach ($sections as $section) {
            $sectionNumber = $section['section_number'];
            $level = substr_count($sectionNumber, '.') + 1;

            if ($level === 1) {
                // 최상위 섹션
                $hierarchy[] = $section;
                $parentMap[$sectionNumber] = &$hierarchy[count($hierarchy) - 1];
            } else {
                // 하위 섹션
                $parentNumber = substr($sectionNumber, 0, strrpos($sectionNumber, '.'));
                if (isset($parentMap[$parentNumber])) {
                    $parentMap[$parentNumber]['subsections'][] = $section;
                    $lastIndex = count($parentMap[$parentNumber]['subsections']) - 1;
                    $parentMap[$sectionNumber] = &$parentMap[$parentNumber]['subsections'][$lastIndex];
                }
            }
        }

        return $hierarchy;
    }

    private function countSubsections(array $sections): int
    {
        $count = 0;

        foreach ($sections as $section) {
            if (!empty($section['subsections'])) {
                $count += count($section['subsections']);
                $count += $this->countSubsections($section['subsections']);
            }
        }

        return $count;
    }
}
