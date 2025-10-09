<?php

namespace App\Services\Rfx\FileManager;

class Service
{
    public function getFiles(array $filters = []): array
    {
        $files = [
            [
                'id' => 1,
                'name' => '프로젝트_계획서_v2.pdf',
                'originalName' => '프로젝트_계획서_v2.pdf',
                'size' => '2.3MB',
                'type' => 'pdf',
                'status' => 'completed',
                'uploadedAt' => '2024-10-09 14:30:00',
                'analyzedAt' => '2024-10-09 14:35:00',
                'tags' => ['계획서', '프로젝트'],
                'summary' => '프로젝트 계획서 문서입니다...',
                'downloadCount' => 5
            ],
            [
                'id' => 2,
                'name' => '데이터_분석_리포트.xlsx',
                'originalName' => '데이터_분석_리포트.xlsx',
                'size' => '1.7MB',
                'type' => 'xlsx',
                'status' => 'analyzing',
                'uploadedAt' => '2024-10-09 13:45:00',
                'analyzedAt' => null,
                'tags' => ['데이터', '분석'],
                'summary' => null,
                'downloadCount' => 2
            ],
            [
                'id' => 3,
                'name' => '회의록_20241009.docx',
                'originalName' => '회의록_20241009.docx',
                'size' => '856KB',
                'type' => 'docx',
                'status' => 'uploaded',
                'uploadedAt' => '2024-10-09 12:15:00',
                'analyzedAt' => null,
                'tags' => ['회의록'],
                'summary' => null,
                'downloadCount' => 1
            ],
            [
                'id' => 4,
                'name' => '기술문서_API_가이드.pdf',
                'originalName' => '기술문서_API_가이드.pdf',
                'size' => '3.2MB',
                'type' => 'pdf',
                'status' => 'completed',
                'uploadedAt' => '2024-10-08 16:20:00',
                'analyzedAt' => '2024-10-08 16:25:00',
                'tags' => ['API', '기술문서'],
                'summary' => 'API 개발 가이드 문서입니다...',
                'downloadCount' => 12
            ],
            [
                'id' => 5,
                'name' => '사용자_매뉴얼.docx',
                'originalName' => '사용자_매뉴얼.docx',
                'size' => '1.1MB',
                'type' => 'docx',
                'status' => 'error',
                'uploadedAt' => '2024-10-08 11:30:00',
                'analyzedAt' => null,
                'tags' => ['매뉴얼'],
                'summary' => null,
                'downloadCount' => 0
            ]
        ];

        // 검색 필터 적용
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $files = array_filter($files, function($file) use ($search) {
                return strpos(strtolower($file['name']), $search) !== false ||
                       strpos(strtolower(implode(' ', $file['tags'])), $search) !== false;
            });
        }

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $files = array_filter($files, function($file) use ($filters) {
                return $file['status'] === $filters['status'];
            });
        }

        // 타입 필터 적용
        if (!empty($filters['type'])) {
            $files = array_filter($files, function($file) use ($filters) {
                return $file['type'] === $filters['type'];
            });
        }

        // 정렬 적용
        if (!empty($filters['sortBy'])) {
            $sortBy = $filters['sortBy'];
            $sortDirection = $filters['sortDirection'] ?? 'asc';
            
            usort($files, function($a, $b) use ($sortBy, $sortDirection) {
                $result = strcmp($a[$sortBy], $b[$sortBy]);
                return $sortDirection === 'desc' ? -$result : $result;
            });
        }

        return array_values($files);
    }

    public function analyzeFile($fileId): array
    {
        return [
            'success' => true,
            'message' => '파일 분석이 시작되었습니다.'
        ];
    }

    public function deleteFile($fileId): array
    {
        return [
            'success' => true,
            'message' => '파일이 삭제되었습니다.'
        ];
    }
}