<?php

namespace App\Services\Rfx\FileManager\GetFiles;

use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute(array $filters = []): array
    {
        $query = UploadedFile::with(['uploader', 'analysis']);

        // 검색 필터 적용
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 타입 필터 적용
        if (!empty($filters['type'])) {
            $query->where('mime_type', 'like', "%{$filters['type']}%");
        }

        // 정렬 적용
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        
        $sortField = match($sortBy) {
            'uploadedAt' => 'created_at',
            'name' => 'original_name',
            'size' => 'file_size',
            default => $sortBy
        };
        
        $query->orderBy($sortField, $sortDirection);

        $files = $query->get();

        return $files->map(function($file) {
            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'originalName' => $file->original_name,
                'size' => $file->formatted_file_size,
                'type' => $this->getFileExtension($file->mime_type),
                'status' => $file->status,
                'uploadedAt' => $file->created_at->format('Y-m-d H:i:s'),
                'analyzedAt' => $file->analyzed_at?->format('Y-m-d H:i:s'),
                'tags' => $file->tags ?? [],
                'summary' => $file->analysis?->summary,
                'downloadCount' => $file->download_count
            ];
        })->toArray();
    }

    private function getFileExtension($mimeType): string
    {
        return match($mimeType) {
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            default => 'unknown'
        };
    }
}