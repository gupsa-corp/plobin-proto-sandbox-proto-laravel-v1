<?php

namespace App\Services\Rfx\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Jobs\Rfx\Upload\ProcessUpload\Jobs as ProcessUploadJob;

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
        if (!$filePath) {
            throw new \Exception("파일 저장에 실패했습니다. putFileAs returned false");
        }

        $fullPath = Storage::disk('local')->path($filePath);
        $fileExists = Storage::disk('local')->exists($filePath);

        if (!$fileExists) {
            throw new \Exception("파일이 저장되지 않았습니다. Path: {$fullPath}");
        }

        // 업로드 ID 생성
        $uploadId = Str::uuid()->toString();

        // 데이터베이스에 업로드 정보 저장
        DB::table('rfx_uploads')->insert([
            'upload_id' => $uploadId,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $filename,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 큐에 OCR 처리 작업 등록
        ProcessUploadJob::dispatch($filePath, $filename, $uploadId);

        return [
            'upload_id' => $uploadId,
            'filename' => $file->getClientOriginalName(),
            'stored_filename' => $filename,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientMimeType(),
            'status' => 'pending'
        ];
    }

    private function generateFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }
}
