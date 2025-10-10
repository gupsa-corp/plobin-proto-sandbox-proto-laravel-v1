<?php

namespace App\Services\Rfx\DocumentAsset\ReorderAssets;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(array $assetOrders): array
    {
        DB::beginTransaction();

        try {
            // assetOrders: [['id' => 'asset_id', 'order' => 0], ...]
            foreach ($assetOrders as $item) {
                DB::table('rfx_document_assets')
                    ->where('id', $item['id'])
                    ->update([
                        'display_order' => $item['order'],
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return ['success' => true];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
