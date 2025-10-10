<?php

namespace App\Services\Rfx\DocumentAsset\ToggleAssetStatus;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(string $assetId): array
    {
        try {
            $asset = DB::table('rfx_document_assets')
                ->where('id', $assetId)
                ->first();

            if (!$asset) {
                return ['success' => false, 'error' => 'Asset not found'];
            }

            // 상태 토글
            $newStatus = $asset->status === 'completed' ? 'pending' : 'completed';
            $newIcon = $newStatus === 'completed' ? '✅' : '⏳';

            DB::table('rfx_document_assets')
                ->where('id', $assetId)
                ->update([
                    'status' => $newStatus,
                    'status_icon' => $newIcon,
                    'updated_at' => now(),
                ]);

            return [
                'success' => true,
                'new_status' => $newStatus,
                'new_icon' => $newIcon
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
