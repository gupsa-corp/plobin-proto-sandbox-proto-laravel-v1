<?php

namespace App\Services\Rfx\FileUpload\ProcessOcrRequest;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function execute(array $data): array
    {
        try {
            Log::info('ProcessOcrRequest 시작', ['data_keys' => array_keys($data)]);

            // 저장된 파일명으로 받은 경우 (기본)
            if (isset($data['stored_name'])) {
                $storedName = $data['stored_name'];
                $filePath = Storage::disk('plobin_uploads')->path($storedName);
                Log::info('저장된 파일명으로 처리', ['stored_name' => $storedName, 'file_path' => $filePath]);

                if (!file_exists($filePath)) {
                    throw new \Exception("파일이 존재하지 않습니다: {$filePath}");
                }

                $fileName = $storedName;
                $fileContents = file_get_contents($filePath);
                $fileSize = strlen($fileContents);
                Log::info('파일 읽기 성공', ['file_name' => $fileName, 'file_size' => $fileSize]);
            }
            // 파일 경로로 받은 경우 (하위 호환성)
            elseif (isset($data['file_path'])) {
                $filePath = $data['file_path'];
                Log::info('파일 경로로 처리', ['file_path' => $filePath]);

                if (!file_exists($filePath)) {
                    throw new \Exception("파일이 존재하지 않습니다: {$filePath}");
                }

                $fileName = basename($filePath);
                $fileContents = file_get_contents($filePath);
                $fileSize = strlen($fileContents);
                Log::info('파일 읽기 성공', ['file_name' => $fileName, 'file_size' => $fileSize]);
            }
            // Livewire UploadedFile 객체로 받은 경우 (하위 호환성)
            elseif (isset($data['file'])) {
                $file = $data['file'];
                $filePath = $file->getRealPath();
                Log::info('Livewire 파일 객체로 처리', ['real_path' => $filePath]);

                if (empty($filePath) || !file_exists($filePath)) {
                    throw new \Exception("유효하지 않은 파일 경로입니다");
                }

                $fileName = $file->getClientOriginalName();
                $fileContents = file_get_contents($filePath);
                Log::info('Livewire 파일 읽기 성공', ['file_name' => $fileName]);
            }
            else {
                throw new \Exception("파일 정보가 제공되지 않았습니다");
            }

            $ocrUrl = config('services.ocr.base_url') . '/process-request';
            Log::info('OCR API 호출 시작', ['url' => $ocrUrl, 'file_name' => $fileName]);

            $response = Http::attach(
                'file',
                $fileContents,
                $fileName
            )->post($ocrUrl);

            Log::info('OCR API 응답 수신', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

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
