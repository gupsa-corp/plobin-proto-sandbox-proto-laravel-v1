<?php

namespace App\Services\Rfx\DocumentAnalysis\List;

use App\Models\Plobin\DocumentAnalysis;

class Service
{
    public function execute(array $filters = []): array
    {
        $query = DocumentAnalysis::with('file');

        // 검색 필터 적용
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('file', function($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%");
            })->orWhere('document_type', 'like', "%{$search}%");
        }

        // 상태 필터 적용
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 날짜 필터 적용
        if (!empty($filters['date'])) {
            $query->whereDate('analyzed_at', $filters['date']);
        }

        $analyses = $query->orderBy('analyzed_at', 'desc')->get();

        return $analyses->map(function($analysis) {
            return [
                'id' => $analysis->file_id,
                'fileName' => $analysis->file->original_name,
                'status' => $analysis->status,
                'analyzedAt' => $analysis->analyzed_at?->format('Y-m-d H:i:s'),
                'confidence' => $analysis->confidence_score,
                'documentType' => $analysis->document_type,
                'keywordCount' => $analysis->keyword_count,
                'pageCount' => $analysis->page_count
            ];
        })->toArray();
    }
}