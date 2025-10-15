<?php

namespace App\Services\Rfx\FileUpload\ProcessOcrRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\Rfx\FileUpload\ProcessOcr\Jobs as ProcessOcrJob;

class Service
{
    public function execute(array $data): array
    {
        try {
            Log::info('ProcessOcrRequest 시작', ['data_keys' => array_keys($data)]);

            // uploaded_file_id 필수 검증
            if (!isset($data['uploaded_file_id']) || empty($data['uploaded_file_id'])) {
                throw new \InvalidArgumentException('uploaded_file_id는 필수 파라미터입니다.');
            }

            $originalName = '';
            $filePath = '';
            $uploadedFileId = $data['uploaded_file_id'];

            // 파일 경로와 원본 파일명으로 받은 경우 (기본)
            if (isset($data['file_path']) && isset($data['original_name'])) {
                $relativePath = $data['file_path'];
                $originalName = $data['original_name'];
                $filePath = Storage::disk('plobin_uploads')->path($relativePath);
                Log::info('파일 경로와 원본 파일명으로 처리', [
                    'original_name' => $originalName,
                    'file_path' => $filePath,
                    'uploaded_file_id' => $uploadedFileId
                ]);

                if (!file_exists($filePath)) {
                    throw new \Exception("파일이 존재하지 않습니다: {$filePath}");
                }
            }
            // 파일 경로만 받은 경우 (하위 호환성)
            elseif (isset($data['file_path'])) {
                $filePath = $data['file_path'];
                $originalName = basename($filePath);
                if (!str_starts_with($filePath, '/')) {
                    $filePath = Storage::disk('plobin_uploads')->path($filePath);
                }
                Log::info('파일 경로로 처리', ['file_path' => $filePath]);

                if (!file_exists($filePath)) {
                    throw new \Exception("파일이 존재하지 않습니다: {$filePath}");
                }
            }
            // Livewire UploadedFile 객체로 받은 경우 (하위 호환성)
            elseif (isset($data['file'])) {
                $file = $data['file'];
                $filePath = $file->getRealPath();
                $originalName = $file->getClientOriginalName();
                Log::info('Livewire 파일 객체로 처리', ['real_path' => $filePath]);

                if (empty($filePath) || !file_exists($filePath)) {
                    throw new \Exception("유효하지 않은 파일 경로입니다");
                }
            }
            else {
                throw new \Exception("파일 정보가 제공되지 않았습니다");
            }

            // 작업 ID 생성
            $jobId = Str::uuid()->toString();

            // 큐에 OCR 처리 작업 등록
            ProcessOcrJob::dispatch($filePath, $originalName, $jobId, $uploadedFileId);

            Log::info('OCR 처리 작업이 큐에 등록되었습니다', [
                'job_id' => $jobId,
                'file_path' => $filePath,
                'original_name' => $originalName
            ]);

            return [
                'success' => true,
                'message' => 'OCR 처리 요청이 큐에 등록되었습니다.',
                'data' => [
                    'job_id' => $jobId,
                    'status' => 'queued'
                ]
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
