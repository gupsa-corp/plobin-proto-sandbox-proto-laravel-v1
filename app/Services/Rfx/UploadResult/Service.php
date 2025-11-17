<?php

namespace App\Services\Rfx\UploadResult;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(string $uploadId): ?array
    {
        $result = DB::table('rfx_ocr_results')
            ->where('upload_id', $uploadId)
            ->first();

        if (!$result) {
            return null;
        }

        return [
            'upload_id' => $result->upload_id,
            'ocr_result' => json_decode($result->ocr_result, true),
            'created_at' => $result->created_at
        ];
    }
}