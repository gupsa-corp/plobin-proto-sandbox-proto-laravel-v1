<?php

namespace App\Services\Rfx\ExternalImport\SaveToDatabase;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Service
{
    public function execute(string $requestId, array $fastApiData): array
    {
        DB::beginTransaction();

        try {
            // 1. rfx_external_imports 업데이트
            DB::table('rfx_external_imports')
                ->where('request_id', $requestId)
                ->update([
                    'original_filename' => $fastApiData['metadata']['original_filename'] ?? null,
                    'total_pages' => $fastApiData['metadata']['total_pages'] ?? 0,
                    'status' => 'importing',
                    'metadata' => json_encode($fastApiData['metadata']),
                    'summary' => json_encode($fastApiData['summary']),
                    'updated_at' => now()
                ]);

            // 2. rfx_document_assets에 저장
            $assetsInserted = 0;
            foreach ($fastApiData['pages'] as $pageData) {
                $pageNumber = $pageData['page_number'] ?? 0;
                $blocks = $pageData['ocr_result']['blocks'] ?? [];

                foreach ($blocks as $index => $block) {
                    DB::table('rfx_document_assets')->insert([
                        'id' => Str::uuid()->toString(),
                        'analysis_request_id' => $requestId,
                        'asset_id' => 'block_' . $pageNumber . '_' . $index,
                        'section_title' => mb_substr($block['text'] ?? '', 0, 255),
                        'asset_type' => $block['block_type'] ?? 'text',
                        'asset_type_name' => $block['block_type'] ?? 'text',
                        'asset_type_icon' => '📄',
                        'content' => $block['text'] ?? '',
                        'page_number' => $pageNumber,
                        'confidence' => $block['confidence'] ?? 0,
                        'display_order' => $index,
                        'status' => 'completed',
                        'status_icon' => '✅',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $assetsInserted++;
                }
            }

            // 3. 완료 처리
            DB::table('rfx_external_imports')
                ->where('request_id', $requestId)
                ->update([
                    'status' => 'completed',
                    'imported_at' => now(),
                    'updated_at' => now()
                ]);

            DB::commit();

            return [
                'success' => true,
                'message' => '데이터 저장 완료',
                'request_id' => $requestId,
                'assets_inserted' => $assetsInserted
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            DB::table('rfx_external_imports')
                ->where('request_id', $requestId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'updated_at' => now()
                ]);

            throw $e;
        }
    }
}
