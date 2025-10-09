<?php

namespace App\Services\Rfx\FileDownload\Download;

use App\Models\Plobin\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Service
{
    public function execute(int $fileId): BinaryFileResponse
    {
        $file = UploadedFile::find($fileId);
        
        if (!$file) {
            throw new NotFoundHttpException('파일을 찾을 수 없습니다');
        }

        $filePath = Storage::disk('plobin_uploads')->path($file->file_path);
        
        if (!Storage::disk('plobin_uploads')->exists($file->file_path)) {
            throw new NotFoundHttpException('파일이 저장소에 존재하지 않습니다');
        }

        // 다운로드 카운트 증가
        $file->increment('download_count');

        return response()->download($filePath, $file->original_name, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $file->original_name . '"'
        ]);
    }
}