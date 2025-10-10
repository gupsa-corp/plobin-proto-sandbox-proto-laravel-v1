<?php

namespace App\Services\Rfx\OcrAnalysis\ProcessWithOntology;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Service
{
    private const API_BASE_URL = 'http://localhost:6003';

    public function execute(string $filePath, string $analysisRequestId): array
    {
        try {
            // 1. OCR API 호출
            $response = Http::attach(
                'file', file_get_contents($filePath), basename($filePath)
            )->post(self::API_BASE_URL . '/analysis/process-with-ontology', [
                'model' => 'boto',
                'language' => 'ko'
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'OCR API 호출 실패: ' . $response->status()
                ];
            }

            $data = $response->json();

            // 2. DB에 저장 (트랜잭션)
            DB::beginTransaction();

            $assets = $data['assets'] ?? [];
            $versionTimestamp = now()->format('YmdHis');

            foreach ($assets as $index => $asset) {
                // 2.1 asset 저장
                $assetId = (string) Str::ulid();

                DB::table('rfx_document_assets')->insert([
                    'id' => $assetId,
                    'analysis_request_id' => $analysisRequestId,
                    'asset_id' => $asset['id'] ?? "asset_" . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'section_title' => $asset['section_title'] ?? '제목 없음',
                    'asset_type' => $asset['asset_type'] ?? 'unknown',
                    'asset_type_name' => $asset['asset_type_name'] ?? '미분류',
                    'asset_type_icon' => $asset['asset_type_icon'] ?? '📄',
                    'content' => $asset['content'] ?? '',
                    'page_number' => $asset['page_number'] ?? 1,
                    'confidence' => $asset['confidence'] ?? 0,
                    'display_order' => $index,
                    'status' => isset($asset['summary']) ? 'completed' : 'pending',
                    'status_icon' => isset($asset['summary']) ? '✅' : '⏳',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2.2 summary 저장 (있는 경우에만)
                if (isset($asset['summary'])) {
                    $summaryId = (string) Str::ulid();

                    DB::table('rfx_asset_summaries')->insert([
                        'id' => $summaryId,
                        'asset_id' => $assetId,
                        'ai_summary' => $asset['summary']['ai_summary'] ?? '',
                        'helpful_content' => $asset['summary']['helpful_content'] ?? '',
                        'confidence' => $asset['summary']['confidence'] ?? 0,
                        'current_version_timestamp' => $versionTimestamp,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 2.3 첫 버전 저장
                    DB::table('rfx_summary_versions')->insert([
                        'id' => (string) Str::ulid(),
                        'summary_id' => $summaryId,
                        'version_timestamp' => $versionTimestamp,
                        'ai_summary' => $asset['summary']['ai_summary'] ?? '',
                        'helpful_content' => $asset['summary']['helpful_content'] ?? '',
                        'edited_by' => 'ai',
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'data' => $data,
                'assets_count' => count($assets)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
