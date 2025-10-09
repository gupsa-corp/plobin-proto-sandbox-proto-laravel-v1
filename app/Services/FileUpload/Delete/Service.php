<?php

namespace App\Services\FileUpload\Delete;

use App\Models\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Service
{
    public function execute(array $data): array
    {
        $fileId = $data['file_id'];
        
        $file = UploadedFile::find($fileId);
        if (!$file) {
            throw new \Exception('파일을 찾을 수 없습니다.', 404);
        }

        return DB::transaction(function () use ($file) {
            // 관련된 분석 결과들 삭제
            foreach ($file->documentAssets as $asset) {
                // AssetSummary와 SummaryVersion 삭제
                foreach ($asset->assetSummaries as $summary) {
                    $summary->summaryVersions()->delete();
                    $summary->delete();
                }
                $asset->delete();
            }

            // 실제 파일 삭제
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // 데이터베이스 레코드 삭제
            $originalName = $file->original_name;
            $file->delete();

            return [
                'message' => "파일 '{$originalName}'이(가) 삭제되었습니다.",
                'deleted_file' => [
                    'id' => $file->id,
                    'original_name' => $originalName,
                ]
            ];
        });
    }
}