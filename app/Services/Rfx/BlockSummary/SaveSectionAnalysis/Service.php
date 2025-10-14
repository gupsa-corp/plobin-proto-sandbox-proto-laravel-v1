<?php

namespace App\Services\Rfx\BlockSummary\SaveSectionAnalysis;

use App\Models\Rfx\SectionAnalysis;
use App\Models\Rfx\SectionVersion;

class Service
{
    /**
     * 섹션 분석 저장 (블록 단위)
     *
     * @param array $data [page_summary_id, block, block_index, ai_summary, helpful_content]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            $timestamp = now()->format('YmdHisu'); // YmdHisu 형식
            $timestampDisplay = now()->format('Y-m-d H:i:s'); // 표시용

            $block = $data['block'];

            // 섹션 분석 레코드 생성
            $section = SectionAnalysis::create([
                'page_summary_id' => $data['page_summary_id'],
                'block_id' => $block['id'] ?? 'unknown',
                'block_index' => $data['block_index'],
                'section_title' => $block['text'] ?? 'Untitled',
                'asset_type' => $data['asset_type'] ?? 'general',
                'asset_type_name' => $data['asset_type_name'] ?? '일반',
                'asset_type_icon' => $data['asset_type_icon'] ?? '📄',
                'original_content' => $block['text'] ?? '',
                'ai_summary' => $data['ai_summary'] ?? '',
                'helpful_content' => $data['helpful_content'] ?? null,
                'current_version_number' => $timestamp,
            ]);

            // 초기 버전 생성
            SectionVersion::create([
                'section_analysis_id' => $section->id,
                'version_number' => $timestamp,
                'version_display_name' => $timestampDisplay . ' (AI 생성)',
                'ai_summary' => $data['ai_summary'] ?? '',
                'is_current' => true,
                'created_by' => 'AI',
                'created_at' => now(),
            ]);

            return [
                'success' => true,
                'data' => $section->load('versions'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to save section analysis: ' . $e->getMessage(),
            ];
        }
    }
}
