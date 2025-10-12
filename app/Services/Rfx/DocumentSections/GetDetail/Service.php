<?php

namespace App\Services\Rfx\DocumentSections\GetDetail;

use App\Services\Rfx\DocumentSections\GetList\Service as GetListService;

class Service
{
    public function execute(string $documentId, string $sectionId, int $page = 1): array
    {
        // 전체 섹션 목록 조회
        $listService = new GetListService();
        $listResult = $listService->execute($documentId, $page);

        if (!$listResult['success']) {
            return $listResult;
        }

        $sections = $listResult['data']['sections'];

        // 특정 섹션 찾기
        $targetSection = $this->findSection($sections, $sectionId);

        if ($targetSection === null) {
            return [
                'success' => false,
                'message' => '섹션을 찾을 수 없습니다',
                'data' => null
            ];
        }

        // 섹션 통계 계산
        $statistics = [
            'total_blocks' => $targetSection['block_count'],
            'average_confidence' => $targetSection['confidence_average'],
            'total_characters' => array_sum(array_map(function($block) {
                return mb_strlen($block['text'] ?? '');
            }, $targetSection['blocks'] ?? []))
        ];

        return [
            'success' => true,
            'message' => '섹션 상세 정보를 성공적으로 조회했습니다',
            'data' => [
                'document_id' => $documentId,
                'document_name' => $listResult['data']['document_name'],
                'page_number' => $page,
                'section' => [
                    'section_id' => $targetSection['section_id'],
                    'section_number' => $targetSection['section_number'],
                    'title' => $targetSection['title'],
                    'parent_section_id' => $this->getParentSectionId($targetSection['section_number']),
                    'blocks' => $targetSection['blocks'] ?? [],
                    'subsections' => array_map(function($subsection) {
                        return [
                            'section_id' => $subsection['section_id'],
                            'section_number' => $subsection['section_number'],
                            'title' => $subsection['title'],
                            'block_count' => $subsection['block_count']
                        ];
                    }, $targetSection['subsections'] ?? []),
                    'statistics' => $statistics
                ]
            ]
        ];
    }

    private function findSection(array $sections, string $sectionId): ?array
    {
        foreach ($sections as $section) {
            if ($section['section_id'] === $sectionId) {
                return $section;
            }

            // 하위 섹션 검색
            if (!empty($section['subsections'])) {
                $found = $this->findSection($section['subsections'], $sectionId);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function getParentSectionId(string $sectionNumber): ?string
    {
        if (strpos($sectionNumber, '.') === false) {
            return null; // 최상위 섹션
        }

        return substr($sectionNumber, 0, strrpos($sectionNumber, '.'));
    }
}
