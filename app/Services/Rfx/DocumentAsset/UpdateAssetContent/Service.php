<?php

namespace App\Services\Rfx\DocumentAsset\UpdateAssetContent;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(string $assetId, string $newContent): array
    {
        try {
            DB::table('rfx_document_assets')
                ->where('id', $assetId)
                ->update([
                    'content' => $newContent,
                    'updated_at' => now(),
                ]);

            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
