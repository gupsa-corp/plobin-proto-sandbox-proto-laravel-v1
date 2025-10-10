<?php

namespace App\Services\Rfx\FileUpload\GetRecentUploads;

use App\Models\Plobin\UploadedFile;
use App\Http\Controllers\Rfx\FileUpload\Response;

class Service
{
    public function execute(array $data = []): array
    {
        try {
            $files = UploadedFile::with('uploader')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->original_name,
                        'size' => $file->formatted_file_size,
                        'type' => strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)),
                        'uploadedAt' => $file->created_at->format('Y-m-d H:i:s'),
                        'status' => $file->status,
                        'uploader' => $file->uploader ? $file->uploader->name : 'Unknown'
                    ];
                })
                ->toArray();

            return Response::success($files);
        } catch (\Exception $e) {
            return Response::error('최근 업로드 파일 조회 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}