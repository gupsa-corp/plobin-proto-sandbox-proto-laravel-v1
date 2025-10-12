<?php

namespace App\Services\Rfx\FileUpload\ProcessOcrRequest;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Service
{
    public function execute(array $data): array
    {
        try {
            // 파일 경로로 받은 경우
            if (isset($data['file_path'])) {
                $filePath = $data['file_path'];

                if (!file_exists($filePath)) {
                    throw new \Exception("파일이 존재하지 않습니다: {$filePath}");
                }

                $fileName = basename($filePath);
                $fileContents = file_get_contents($filePath);
            }
            // Livewire UploadedFile 객체로 받은 경우 (하위 호환성)
            elseif (isset($data['file'])) {
                $file = $data['file'];
                $filePath = $file->getRealPath();

                if (empty($filePath) || !file_exists($filePath)) {
                    throw new \Exception("유효하지 않은 파일 경로입니다");
                }

                $fileName = $file->getClientOriginalName();
                $fileContents = file_get_contents($filePath);
            }
            else {
                throw new \Exception("파일 정보가 제공되지 않았습니다");
            }

            $response = Http::attach(
                'file',
                $fileContents,
                $fileName
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
            Log::error('OCR 처리 요청 실패: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'OCR 처리 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
