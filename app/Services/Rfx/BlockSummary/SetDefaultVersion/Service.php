<?php

namespace App\Services\Rfx\BlockSummary\SetDefaultVersion;

use App\Models\Rfx\SectionAnalysis;
use App\Models\Rfx\SectionVersion;

class Service
{
    /**
     * 선택한 버전을 기본값으로 설정
     *
     * @param array $data [section_id, version_id]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            $section = SectionAnalysis::findOrFail($data['section_id']);
            $selectedVersion = SectionVersion::findOrFail($data['version_id']);

            // 버전이 해당 섹션의 것인지 확인
            if ($selectedVersion->section_analysis_id !== $section->id) {
                return [
                    'success' => false,
                    'message' => '선택한 버전이 해당 섹션에 속하지 않습니다',
                ];
            }

            // 모든 버전의 is_current를 false로 변경
            SectionVersion::where('section_analysis_id', $section->id)
                ->update(['is_current' => false]);

            // 선택한 버전을 is_current = true로 변경
            $selectedVersion->update(['is_current' => true]);

            // 섹션의 current_version_number와 ai_summary 업데이트
            $section->update([
                'current_version_number' => $selectedVersion->version_number,
                'ai_summary' => $selectedVersion->ai_summary,
            ]);

            return [
                'success' => true,
                'data' => $selectedVersion,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to set default version: ' . $e->getMessage(),
            ];
        }
    }
}
