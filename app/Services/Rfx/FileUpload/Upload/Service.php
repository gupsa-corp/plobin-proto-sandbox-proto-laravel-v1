<?php

namespace App\Services\Rfx\FileUpload\Upload;

use App\Models\Plobin\UploadedFile;
use App\Http\Controllers\Rfx\FileUpload\Response;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Service
{
    public function execute($data): array
    {
        try {
            // Livewire에서 배열로 전달받는 경우와 HTTP Request 객체 구분
            if (is_array($data)) {
                $file = $data['file'];
                $description = $data['description'] ?? '';
                $tags = $data['tags'] ?? [];
            } else {
                // HTTP Request 객체인 경우 (API 호출)
                $file = $data->file('file');
                $description = $data->input('description');
                $tags = $data->input('tags', []);
            }
            
            $originalName = $file->getClientOriginalName();
            $storedName = Str::uuid() . '_' . $originalName;
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            
            $filePath = $file->storeAs('', $storedName, 'plobin_uploads');
            
            $uploadedFile = UploadedFile::create([
                'original_name' => $originalName,
                'stored_name' => $storedName,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'status' => 'uploaded',
                'uploaded_by' => 1, // 기본 테스트 사용자 ID
                'tags' => $tags,
                'description' => $description
            ]);
            
            // 파일 타입 추출 (확장자 기반)
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            return Response::success([
                'id' => $uploadedFile->id,
                'name' => $uploadedFile->original_name,
                'size' => $uploadedFile->formatted_file_size,
                'type' => $fileExtension,
                'uploadedAt' => $uploadedFile->created_at->format('Y-m-d H:i:s'),
                'status' => $uploadedFile->status,
                'uploader' => 'Admin User'
            ]);
            
        } catch (\Exception $e) {
            return Response::error('파일 업로드 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}