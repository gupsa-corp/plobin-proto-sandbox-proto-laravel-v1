<?php

namespace App\Services\Rfx\BlockSummary\UpdateSectionVersion;

use App\Models\Rfx\SectionAnalysis;
use App\Models\Rfx\SectionVersion;

class Service
{
    /**
     * 섹션 버전 업데이트 (새 버전 생성)
     *
     * @param array $data [section_id, new_summary, user_id]
     * @return array [success, data/message]
     */
    public function execute(array $data): array
    {
        try {
            $section = SectionAnalysis::findOrFail($data['section_id']);

            $timestamp = now()->format('YmdHisu'); // YmdHisu 형식
            $timestampDisplay = now()->format('Y-m-d H:i:s'); // 표시용

            // 기존 버전들의 is_current를 false로 변경
            SectionVersion::where('section_analysis_id', $section->id)
                ->update(['is_current' => false]);

            // 새 버전 생성
            $newVersion = SectionVersion::create([
                'section_analysis_id' => $section->id,
                'version_number' => $timestamp,
                'version_display_name' => $timestampDisplay . ' (사용자 편집)',
                'ai_summary' => $data['new_summary'],
                'is_current' => true,
                'created_by' => $data['user_id'] ?? 'user',
                'created_at' => now(),
            ]);

            // 섹션의 current_version_number 업데이트
            $section->update([
                'current_version_number' => $timestamp,
                'ai_summary' => $data['new_summary'],
            ]);

            return [
                'success' => true,
                'data' => $newVersion,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update section version: ' . $e->getMessage(),
            ];
        }
    }
}
