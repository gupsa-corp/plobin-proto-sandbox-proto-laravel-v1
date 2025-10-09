<?php

namespace App\Services\Rfx\DocumentAnalysis\GetResult;

use App\Models\Plobin\UploadedFile;

class Service
{
    public function execute($documentId): array
    {
        $file = UploadedFile::find($documentId);
        
        if (!$file) {
            return [
                'summary' => '파일을 찾을 수 없습니다.',
                'keywords' => [],
                'categories' => [],
                'confidence' => 0,
                'extractedData' => [],
                'recommendations' => []
            ];
        }

        $analysis = $file->analysis;
        
        if (!$analysis || $analysis->status !== 'completed') {
            return [
                'summary' => '분석 결과가 없습니다.',
                'keywords' => [],
                'categories' => [],
                'confidence' => 0,
                'extractedData' => [],
                'recommendations' => []
            ];
        }

        return [
            'summary' => $analysis->summary ?? '요약이 없습니다.',
            'keywords' => $analysis->keywords ?? [],
            'categories' => $analysis->categories ?? [],
            'confidence' => $analysis->confidence_score ?? 0,
            'extractedData' => $analysis->extracted_data ?? [],
            'recommendations' => $analysis->recommendations ?? []
        ];
    }
}