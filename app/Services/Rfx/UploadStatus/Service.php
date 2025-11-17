<?php

namespace App\Services\Rfx\UploadStatus;

use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(string $uploadId): ?array
    {
        $upload = DB::table('rfx_uploads')
            ->where('upload_id', $uploadId)
            ->first();

        if (!$upload) {
            return null;
        }

        return [
            'upload_id' => $upload->upload_id,
            'status' => $upload->status,
            'original_filename' => $upload->original_filename,
            'file_size' => $upload->file_size,
            'file_type' => $upload->file_type,
            'created_at' => $upload->created_at,
            'updated_at' => $upload->updated_at,
            'error_message' => $upload->error_message
        ];
    }
}