<?php

namespace App\Services\Rfx\FileUpload\ProcessOcrRequest;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(array $data): array
    {
        try {
            $file = $data['file'];

            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post(config('services.ocr.base_url') . '/process-request');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'OCR 처리 요청이 성공적으로 완료되었습니다.',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'OCR 처리 요청에 실패했습니다.',
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('OCR 처리 요청 실패: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'OCR 처리 중 오류가 발생했습니다.',
                'data' => null
            ];
        }
    }
}
