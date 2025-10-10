<?php

namespace App\Services\Rfx\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Service
{
    public function execute(array $data): array
    {
        $file = $data['file'];
        
        // 디렉토리 생성
        $uploadDir = 'uploads/rfx';
        if (!Storage::disk('local')->exists($uploadDir)) {
            Storage::disk('local')->makeDirectory($uploadDir);
        }
        
        // 파일 저장
        $filename = $this->generateFilename($file);
        $filePath = Storage::disk('local')->putFileAs($uploadDir, $file, $filename);
        
        // 파일 저장 확인
        $fullPath = Storage::disk('local')->path($uploadDir . '/' . $filename);
        $fileExists = Storage::disk('local')->exists($uploadDir . '/' . $filename);
        
        if (!$filePath || !$fileExists) {
            throw new \Exception("파일 저장에 실패했습니다. Path: {$fullPath}, Exists: " . ($fileExists ? 'true' : 'false'));
        }
        
        // OCR 서비스로 전송
        $ocrResult = $this->sendToOcrService($file);
        
        return [
            'upload_id' => Str::uuid(),
            'filename' => $filename,
            'file_path' => $filePath,
            'ocr_result' => $ocrResult
        ];
    }

    private function generateFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    private function sendToOcrService(UploadedFile $file): array
    {
        $ocrServiceUrl = config('app.ocr_service_url');
        
        try {
            $response = Http::attach(
                'file', 
                $file->getContent(),
                $file->getClientOriginalName()
            )->post($ocrServiceUrl . '/process-image');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'OCR 서비스 요청 실패',
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'OCR 서비스 연결 실패: ' . $e->getMessage()
            ];
        }
    }
}