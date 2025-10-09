<?php

namespace App\Services\FileUpload\Create;

use App\Models\UploadedFile;
use App\Services\FileUpload\ValidateFileUpload\Service as ValidateFileUploadService;
use App\Services\FileUpload\ValidateFileSecurity\Service as ValidateFileSecurityService;
use App\Services\FileUpload\GenerateUniqueFilename\Service as GenerateUniqueFilenameService;
use App\Services\FileUpload\GetFileTypeDisplay\Service as GetFileTypeDisplayService;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Service
{
    private const MAX_TOTAL_SIZE = 50 * 1024 * 1024; // 50MB total

    public function execute(array $data): array
    {
        $file = $data['file'];
        $options = $data['options'] ?? [];
        
        if (!$file instanceof HttpUploadedFile) {
            throw new \InvalidArgumentException('Valid file is required');
        }

        // 비즈니스 로직 검증
        (new ValidateFileUploadService())->execute($file);

        // 보안 검증
        (new ValidateFileSecurityService())->execute($file);

        // 전체 파일 크기 검증
        $uploadedBy = $options['uploaded_by'] ?? 'system';
        $totalSize = UploadedFile::where('is_analysis_completed', true)->sum('file_size');
        
        if ($totalSize + $file->getSize() > self::MAX_TOTAL_SIZE) {
            throw new \Exception('총 파일 크기가 50MB를 초과합니다.');
        }

        return DB::transaction(function () use ($file, $options) {
            // 파일 저장
            $filename = (new GenerateUniqueFilenameService())->execute($file);
            $path = $file->storeAs('uploads', $filename, 'public');

            // 데이터베이스에 저장
            $uploadedFile = UploadedFile::create([
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_analysis_requested' => false,
                'is_analysis_completed' => false,
                'analysis_status' => 'pending',
            ]);

            return [
                'id' => $uploadedFile->id,
                'original_name' => $uploadedFile->original_name,
                'stored_name' => $uploadedFile->file_name,
                'file_size' => $uploadedFile->file_size,
                'file_size_mb' => $uploadedFile->file_size_formatted,
                'file_path' => $uploadedFile->file_path,
                'status' => 'uploaded',
                'uploaded_at' => $uploadedFile->created_at->format('Y-m-d H:i:s'),
                'file_type_display' => (new GetFileTypeDisplayService())->execute($uploadedFile->mime_type),
                'is_image' => strpos($uploadedFile->mime_type, 'image/') === 0,
            ];
        });
    }
}