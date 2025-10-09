<?php

namespace App\Services\Rfx\FileUpload\Upload;

use App\Models\Plobin\UploadedFile;
use App\Http\Controllers\Rfx\FileUpload\Response;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Service
{
    public function execute($request): array
    {
        try {
            $file = $request->file('file');
            $description = $request->input('description');
            $tags = $request->input('tags', []);
            
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
                'uploaded_by' => 1, // TODO: 실제 인증된 사용자 ID로 변경
                'tags' => $tags,
                'description' => $description
            ]);
            
            return Response::success([
                'file_id' => $uploadedFile->id,
                'original_name' => $uploadedFile->original_name,
                'file_size' => $uploadedFile->file_size,
                'formatted_size' => $uploadedFile->formatted_file_size,
                'status' => $uploadedFile->status,
                'uploaded_at' => $uploadedFile->created_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return Response::error('파일 업로드 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}