<?php

namespace App\Services\FileUpload\Create;

use App\Models\UploadedFile;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Service
{
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB per file
    private const MAX_TOTAL_SIZE = 50 * 1024 * 1024; // 50MB total
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'text/csv',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    public function execute(array $data): array
    {
        $file = $data['file'];
        $options = $data['options'] ?? [];
        
        if (!$file instanceof HttpUploadedFile) {
            throw new \InvalidArgumentException('Valid file is required');
        }

        // 비즈니스 로직 검증
        $this->validateFileUpload($file);

        // 보안 검증
        $this->validateFileSecurity($file);

        // 전체 파일 크기 검증
        $uploadedBy = $options['uploaded_by'] ?? 'system';
        $totalSize = UploadedFile::where('is_analysis_completed', true)->sum('file_size');
        
        if ($totalSize + $file->getSize() > self::MAX_TOTAL_SIZE) {
            throw new \Exception('총 파일 크기가 50MB를 초과합니다.');
        }

        return DB::transaction(function () use ($file, $options) {
            // 파일 저장
            $filename = $this->generateUniqueFilename($file);
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
                'file_type_display' => $this->getFileTypeDisplay($uploadedFile->mime_type),
                'is_image' => strpos($uploadedFile->mime_type, 'image/') === 0,
            ];
        });
    }

    private function validateFileUpload(HttpUploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('파일 업로드 중 오류가 발생했습니다.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('파일 크기가 너무 큽니다. 최대 10MB까지 허용됩니다.');
        }

        if (empty($file->getClientOriginalName())) {
            throw new \Exception('파일명이 필요합니다.');
        }
    }

    private function validateFileSecurity(HttpUploadedFile $file): void
    {
        // MIME 타입 검증
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('허용되지 않은 파일 형식입니다.');
        }

        // 파일 확장자 검증
        $extension = strtolower($file->getClientOriginalExtension());
        $dangerousExtensions = ['php', 'js', 'html', 'exe', 'bat', 'sh'];
        
        if (in_array($extension, $dangerousExtensions)) {
            throw new \Exception('보안상 허용되지 않은 파일 확장자입니다.');
        }

        // 파일명 보안 검증
        $filename = $file->getClientOriginalName();
        if (preg_match('/[<>:"|?*]/', $filename)) {
            throw new \Exception('파일명에 허용되지 않은 문자가 포함되어 있습니다.');
        }
    }

    private function generateUniqueFilename(HttpUploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        return "{$timestamp}_{$originalName}.{$extension}";
    }

    private function getFileTypeDisplay($mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) return '이미지';
        if (strpos($mimeType, 'video/') === 0) return '비디오';
        if (strpos($mimeType, 'audio/') === 0) return '오디오';
        if ($mimeType === 'application/pdf') return 'PDF';
        if (strpos($mimeType, 'application/msword') === 0 || 
            strpos($mimeType, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0) {
            return 'Word 문서';
        }
        if (strpos($mimeType, 'application/vnd.ms-excel') === 0 || 
            strpos($mimeType, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0) {
            return 'Excel 문서';
        }
        if (strpos($mimeType, 'text/') === 0) return '텍스트';
        
        return '기타';
    }
}