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
            $uuid = (string) Str::uuid();
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();

            $filePath = $file->storeAs('', $originalName, 'plobin_uploads');

            // plobin_users 테이블에 기본 사용자가 없으면 생성
            $defaultUser = \Illuminate\Support\Facades\DB::table('plobin_users')
                ->where('email', 'admin@example.com')
                ->first();

            if (!$defaultUser) {
                \Illuminate\Support\Facades\DB::table('plobin_users')->insert([
                    'email' => 'admin@example.com',
                    'name' => 'Admin User',
                    'role' => 'admin',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $defaultUser = \Illuminate\Support\Facades\DB::table('plobin_users')
                    ->where('email', 'admin@example.com')
                    ->first();
            }

            $uploadedFile = UploadedFile::create([
                'uuid' => $uuid,
                'original_name' => $originalName,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'status' => 'uploaded',
                'uploaded_by' => $defaultUser->id,
                'tags' => $tags,
                'description' => $description
            ]);
            
            // 파일 타입 추출 (확장자 기반)
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            return Response::success([
                'id' => $uploadedFile->id,
                'uuid' => $uploadedFile->uuid,
                'name' => $uploadedFile->original_name,
                'file_path' => $uploadedFile->file_path,
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