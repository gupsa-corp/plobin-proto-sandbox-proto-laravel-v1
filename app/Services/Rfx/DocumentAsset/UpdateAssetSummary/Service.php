<?php

namespace App\Services\Rfx\DocumentAsset\UpdateAssetSummary;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Service
{
    public function execute(string $assetId, string $newSummary): array
    {
        DB::beginTransaction();

        try {
            // 1. 현재 summary 조회
            $summary = DB::table('rfx_asset_summaries')
                ->where('asset_id', $assetId)
                ->first();

            if (!$summary) {
                return ['success' => false, 'error' => 'Summary not found'];
            }

            // 2. 새 버전 타임스탬프 생성
            $newVersionTimestamp = now()->format('YmdHis');

            // 3. 새 버전을 히스토리에 저장
            DB::table('rfx_summary_versions')->insert([
                'id' => (string) Str::ulid(),
                'summary_id' => $summary->id,
                'version_timestamp' => $newVersionTimestamp,
                'ai_summary' => $newSummary,
                'helpful_content' => $summary->helpful_content,
                'edited_by' => 'user',
                'created_at' => now(),
            ]);

            // 4. 현재 summary 업데이트
            DB::table('rfx_asset_summaries')
                ->where('id', $summary->id)
                ->update([
                    'ai_summary' => $newSummary,
                    'current_version_timestamp' => $newVersionTimestamp,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return [
                'success' => true,
                'version' => $newVersionTimestamp
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
