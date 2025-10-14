<?php

namespace App\Services\Rfx\BlockSummary\GetPageBlocks;

class Service
{
    /**
     * 블록을 페이지별로 그룹화
     *
     * @param array $blocks 모든 블록 배열
     * @return array 페이지별로 그룹화된 블록 배열
     */
    public function execute(array $blocks): array
    {
        $pageGroups = [];

        foreach ($blocks as $block) {
            $pageNumber = $block['page'] ?? 1;

            if (!isset($pageGroups[$pageNumber])) {
                $pageGroups[$pageNumber] = [
                    'page_number' => $pageNumber,
                    'blocks' => [],
                    'block_count' => 0,
                ];
            }

            $pageGroups[$pageNumber]['blocks'][] = $block;
            $pageGroups[$pageNumber]['block_count']++;
        }

        // 페이지 번호순으로 정렬
        ksort($pageGroups);

        return array_values($pageGroups);
    }
}
