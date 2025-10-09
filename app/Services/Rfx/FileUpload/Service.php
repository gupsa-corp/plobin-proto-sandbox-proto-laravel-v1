<?php

namespace App\Services\Rfx\FileUpload;

class Service
{
    public function uploadFile($file): array
    {
        // 파일 업로드 시뮬레이션
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileType = $file->getClientOriginalExtension();
        
        return [
            'success' => true,
            'data' => [
                'id' => uniqid(),
                'name' => $fileName,
                'size' => $this->formatFileSize($fileSize),
                'type' => $fileType,
                'uploadedAt' => now()->format('Y-m-d H:i:s'),
                'status' => 'uploaded'
            ]
        ];
    }

    public function getRecentUploads(): array
    {
        return [
            [
                'id' => 1,
                'name' => '프로젝트_계획서.pdf',
                'size' => '2.3MB',
                'type' => 'pdf',
                'uploadedAt' => '2024-10-09 14:30:00',
                'status' => 'uploaded'
            ],
            [
                'id' => 2,
                'name' => '데이터_분석_리포트.xlsx',
                'size' => '1.7MB',
                'type' => 'xlsx',
                'uploadedAt' => '2024-10-09 13:45:00',
                'status' => 'analyzing'
            ],
            [
                'id' => 3,
                'name' => '회의록_20241009.docx',
                'size' => '856KB',
                'type' => 'docx',
                'uploadedAt' => '2024-10-09 12:15:00',
                'status' => 'completed'
            ]
        ];
    }

    private function formatFileSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}