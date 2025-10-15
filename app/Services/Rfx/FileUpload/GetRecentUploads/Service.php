<?php

namespace App\Services\Rfx\FileUpload\GetRecentUploads;

use App\Models\Plobin\UploadedFile;
use App\Http\Controllers\Rfx\FileUpload\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                    $ocrStatus = 'pending';
                    $ocrResult = null;

                    // OCR request_id가 있으면 OCR API에서 상태 조회
                    if ($file->ocr_request_id) {
                        try {
                            $response = Http::timeout(5)->get(
                                config('services.ocr.base_url') . "/requests/{$file->ocr_request_id}"
                            );

                            if ($response->successful()) {
                                $ocrData = $response->json();
                                $ocrStatus = $ocrData['status'] ?? 'pending';
                                $ocrResult = $ocrData;
                            }
                        } catch (\Exception $e) {
                            Log::warning('OCR 상태 조회 실패', [
                                'file_id' => $file->id,
                                'ocr_request_id' => $file->ocr_request_id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    return [
                        'id' => $file->id,
                        'name' => $file->original_name,
                        'size' => $file->formatted_file_size,
                        'type' => strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION)),
                        'uploadedAt' => $file->created_at->format('Y-m-d H:i:s'),
                        'status' => $file->status,
                        'uploader' => $file->uploader ? $file->uploader->name : 'Unknown',
                        'ocr_request_id' => $file->ocr_request_id,
                        'ocr_status' => $ocrStatus,
                        'ocr_result' => $ocrResult
                    ];
                })
                ->toArray();

            return Response::success($files);
        } catch (\Exception $e) {
            return Response::error('최근 업로드 파일 조회 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}