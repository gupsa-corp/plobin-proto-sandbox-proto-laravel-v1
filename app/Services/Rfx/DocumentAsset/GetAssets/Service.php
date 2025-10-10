<?php

namespace App\Services\Rfx\DocumentAsset\GetAssets;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(string $analysisRequestId): array
    {
        try {
            $assets = DB::table('rfx_document_assets as a')
                ->leftJoin('rfx_asset_summaries as s', 'a.id', '=', 's.asset_id')
                ->where('a.analysis_request_id', $analysisRequestId)
                ->orderBy('a.display_order')
                ->select([
                    'a.*',
                    's.id as summary_id',
                    's.ai_summary',
                    's.helpful_content',
                    's.current_version_timestamp',
                    's.confidence as summary_confidence'
                ])
                ->get();

            $assetsData = [];

            foreach ($assets as $asset) {
                $assetArray = [
                    'id' => $asset->id,
                    'asset_id' => $asset->asset_id,
                    'section_title' => $asset->section_title,
                    'asset_type' => $asset->asset_type,
                    'asset_type_name' => $asset->asset_type_name,
                    'asset_type_icon' => $asset->asset_type_icon,
                    'content' => $asset->content,
                    'page_number' => $asset->page_number,
                    'confidence' => $asset->confidence,
                    'display_order' => $asset->display_order,
                    'status' => $asset->status,
                    'status_icon' => $asset->status_icon,
                    'created_at' => $asset->created_at,
                ];

                // summary가 있는 경우
                if ($asset->summary_id) {
                    // 버전 히스토리 조회
                    $versions = DB::table('rfx_summary_versions')
                        ->where('summary_id', $asset->summary_id)
                        ->orderBy('version_timestamp', 'desc')
                        ->get();

                    $assetArray['summary'] = [
                        'ai_summary' => $asset->ai_summary,
                        'helpful_content' => $asset->helpful_content,
                        'confidence' => $asset->summary_confidence,
                        'current_version_timestamp' => $asset->current_version_timestamp,
                        'versions' => $versions->map(function($v) {
                            return [
                                'version_timestamp' => $v->version_timestamp,
                                'ai_summary' => $v->ai_summary,
                                'helpful_content' => $v->helpful_content,
                                'edited_by' => $v->edited_by,
                                'created_at' => $v->created_at,
                            ];
                        })->toArray()
                    ];
                }

                $assetsData[] = $assetArray;
            }

            return [
                'success' => true,
                'data' => $assetsData
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
