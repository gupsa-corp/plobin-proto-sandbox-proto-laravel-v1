<?php

namespace App\Services\Rfx\AiAnalysisToDocumentAssets\Migrate;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Service
{
    public function execute(int $aiAnalysisRequestId): array
    {
        try {
            // AI Analysis Request 조회
            $aiRequest = DB::table('rfx_ai_analysis_requests')
                ->where('id', $aiAnalysisRequestId)
                ->where('status', 'completed')
                ->first();

            if (!$aiRequest) {
                return [
                    'success' => false,
                    'error' => 'AI 분석 요청을 찾을 수 없거나 완료되지 않았습니다.'
                ];
            }

            // result JSON 파싱
            $result = json_decode($aiRequest->result, true);

            if (!$result || !isset($result['sections'])) {
                return [
                    'success' => false,
                    'error' => 'AI 분석 결과에 섹션 정보가 없습니다.'
                ];
            }

            DB::beginTransaction();

            // Document Analysis 생성
            $documentAnalysisId = DB::table('plobin_document_analyses')->insertGetId([
                'file_id' => $aiRequest->file_id,
                'status' => 'completed',
                'summary' => json_encode([
                    'total_sections' => count($result['sections']),
                    'migrated_from_ai_analysis' => $aiAnalysisRequestId,
                ]),
                'extracted_data' => json_encode($result),
                'document_type' => $aiRequest->file_type,
                'page_count' => $result['total_pages'] ?? 1,
                'analyzed_at' => $aiRequest->completed_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sections를 Document Assets로 변환
            $displayOrder = 1;
            foreach ($result['sections'] as $section) {
                $assetId = Str::uuid()->toString();

                DB::table('rfx_document_assets')->insert([
                    'analysis_request_id' => $aiAnalysisRequestId,
                    'asset_id' => $assetId,
                    'section_title' => $section['title'] ?? '제목 없음',
                    'asset_type' => $this->mapAssetType($section['type'] ?? 'text'),
                    'asset_type_name' => $this->mapAssetTypeName($section['type'] ?? 'text'),
                    'asset_type_icon' => $this->mapAssetTypeIcon($section['type'] ?? 'text'),
                    'content' => $section['content'] ?? '',
                    'page_number' => $section['page'] ?? 1,
                    'confidence' => $section['confidence'] ?? 0.95,
                    'display_order' => $displayOrder++,
                    'status' => 'active',
                    'status_icon' => '✓',
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'document_analysis_id' => $documentAnalysisId,
                'assets_created' => count($result['sections']),
                'message' => 'AI 분석 결과를 Document Assets로 성공적으로 마이그레이션했습니다.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function mapAssetType(string $type): string
    {
        $mapping = [
            'text' => 'text',
            'heading' => 'heading',
            'table' => 'table',
            'list' => 'list',
            'image' => 'image',
            'code' => 'code',
        ];

        return $mapping[$type] ?? 'text';
    }

    private function mapAssetTypeName(string $type): string
    {
        $mapping = [
            'text' => '텍스트',
            'heading' => '제목',
            'table' => '표',
            'list' => '목록',
            'image' => '이미지',
            'code' => '코드',
        ];

        return $mapping[$type] ?? '텍스트';
    }

    private function mapAssetTypeIcon(string $type): string
    {
        $mapping = [
            'text' => '📄',
            'heading' => '📌',
            'table' => '📊',
            'list' => '📋',
            'image' => '🖼️',
            'code' => '💻',
        ];

        return $mapping[$type] ?? '📄';
    }
}
