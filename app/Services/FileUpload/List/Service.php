<?php

namespace App\Services\FileUpload\List;

use App\Models\UploadedFile;
use Illuminate\Database\Eloquent\Builder;

class Service
{
    public function execute(array $data): array
    {
        $search = $data['search'] ?? null;
        $status = $data['status'] ?? null;
        $type = $data['type'] ?? null;
        $limit = max(1, min(100, $data['limit'] ?? 20)); // 1-100 사이로 제한
        $offset = max(0, $data['offset'] ?? 0);
        $sort = $data['sort'] ?? 'created_at';
        $direction = in_array($data['direction'] ?? 'desc', ['asc', 'desc']) ? $data['direction'] : 'desc';

        $query = UploadedFile::query();

        // 검색 조건 적용
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // 상태 필터
        if ($status) {
            $query->where('analysis_status', $status);
        }

        // 파일 타입 필터
        if ($type) {
            switch ($type) {
                case 'pdf':
                    $query->where('mime_type', 'application/pdf');
                    break;
                case 'doc':
                    $query->whereIn('mime_type', [
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ]);
                    break;
                case 'xls':
                    $query->whereIn('mime_type', [
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ]);
                    break;
                case 'txt':
                    $query->whereIn('mime_type', ['text/plain', 'text/csv']);
                    break;
                case 'image':
                    $query->where('mime_type', 'like', 'image/%');
                    break;
            }
        }

        // 정렬 적용
        $allowedSortFields = ['original_name', 'file_size', 'mime_type', 'analysis_status', 'created_at'];
        if (in_array($sort, $allowedSortFields)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 총 개수 조회
        $total = $query->count();

        // 페이지네이션 적용
        $files = $query->skip($offset)
                      ->take($limit)
                      ->with(['documentAssets']) // 분석 결과 포함
                      ->get()
                      ->map(function ($file) {
                          return [
                              'id' => $file->id,
                              'original_name' => $file->original_name,
                              'file_name' => $file->file_name,
                              'file_path' => $file->file_path,
                              'file_size' => $file->file_size,
                              'file_size_formatted' => $file->file_size_formatted,
                              'mime_type' => $file->mime_type,
                              'analysis_status' => $file->analysis_status,
                              'is_analysis_requested' => $file->is_analysis_requested,
                              'is_analysis_completed' => $file->is_analysis_completed,
                              'analysis_completed_at' => $file->analysis_completed_at?->format('Y-m-d H:i:s'),
                              'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                              'updated_at' => $file->updated_at->format('Y-m-d H:i:s'),
                              'document_assets' => $file->documentAssets->map(function ($asset) {
                                  return [
                                      'id' => $asset->id,
                                      'asset_type' => $asset->asset_type,
                                      'section_title' => $asset->section_title,
                                      'status' => $asset->status,
                                  ];
                              }),
                          ];
                      });

        return [
            'files' => $files,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'has_next' => $offset + $limit < $total,
            'has_prev' => $offset > 0,
        ];
    }
}