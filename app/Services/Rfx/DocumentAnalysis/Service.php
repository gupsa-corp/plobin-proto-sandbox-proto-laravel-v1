<?php

namespace App\Services\Rfx\DocumentAnalysis;

class Service
{
    public function execute(array $filters = []): array
    {
        $documents = [
            [
                'id' => 1,
                'fileName' => '프로젝트_계획서_v2.pdf',
                'status' => 'completed',
                'analyzedAt' => '2024-10-09 14:35:00',
                'confidence' => 95.8,
                'documentType' => '계획서',
                'keywordCount' => 127,
                'pageCount' => 24
            ],
            [
                'id' => 2,
                'fileName' => '데이터_분석_리포트.xlsx',
                'status' => 'analyzing',
                'analyzedAt' => null,
                'confidence' => null,
                'documentType' => '분석 리포트',
                'keywordCount' => null,
                'pageCount' => 15
            ],
            [
                'id' => 3,
                'fileName' => '회의록_20241009.docx',
                'status' => 'pending',
                'analyzedAt' => null,
                'confidence' => null,
                'documentType' => '회의록',
                'keywordCount' => null,
                'pageCount' => 8
            ],
            [
                'id' => 4,
                'fileName' => '기술문서_API_가이드.pdf',
                'status' => 'completed',
                'analyzedAt' => '2024-10-08 16:25:00',
                'confidence' => 89.2,
                'documentType' => '기술문서',
                'keywordCount' => 203,
                'pageCount' => 45
            ],
            [
                'id' => 5,
                'fileName' => '사용자_매뉴얼.docx',
                'status' => 'error',
                'analyzedAt' => null,
                'confidence' => null,
                'documentType' => '매뉴얼',
                'keywordCount' => null,
                'pageCount' => 32
            ]
        ];

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $documents = array_filter($documents, function($doc) use ($search) {
                return strpos(strtolower($doc['fileName']), $search) !== false ||
                       strpos(strtolower($doc['documentType']), $search) !== false;
            });
        }

        if (!empty($filters['status'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                return $doc['status'] === $filters['status'];
            });
        }

        if (!empty($filters['date'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                if (!$doc['analyzedAt']) return false;
                return date('Y-m-d', strtotime($doc['analyzedAt'])) === $filters['date'];
            });
        }

        return array_values($documents);
    }

    public function getAnalysisResult($documentId): array
    {
        $analysisResults = [
            1 => [
                'summary' => '이 문서는 2024년 프로젝트 계획서로, 주요 목표와 일정, 예산 배분에 대한 내용을 담고 있습니다.',
                'keywords' => ['프로젝트 관리', '일정 계획', '예산 편성', '리스크 관리', '성과 지표'],
                'categories' => ['계획서', '프로젝트 문서', '관리 문서'],
                'confidence' => 95.8,
                'extractedData' => [
                    '프로젝트명' => '디지털 전환 프로젝트',
                    '시작일' => '2024-01-15',
                    '종료일' => '2024-12-31',
                    '예산' => '500,000,000원',
                    '담당자' => '김프로젝트'
                ],
                'recommendations' => [
                    '일정 관리를 위한 추가 체크포인트 설정 권장',
                    '리스크 대응 계획의 구체화 필요',
                    '성과 측정 지표의 명확한 정의 필요'
                ]
            ],
            4 => [
                'summary' => 'API 개발 가이드라인과 사용법을 설명하는 기술 문서입니다.',
                'keywords' => ['API', 'REST', 'JSON', '인증', '예제'],
                'categories' => ['기술문서', 'API 문서', '개발 가이드'],
                'confidence' => 89.2,
                'extractedData' => [
                    'API 버전' => 'v2.1',
                    '지원 형식' => 'JSON, XML',
                    '인증 방식' => 'OAuth 2.0',
                    '베이스 URL' => 'https://api.example.com/v2'
                ],
                'recommendations' => [
                    '에러 코드 설명 섹션 추가 권장',
                    '실제 사용 예제 확대 필요',
                    '버전 업데이트 내역 문서화 필요'
                ]
            ]
        ];

        return $analysisResults[$documentId] ?? [
            'summary' => '분석 결과가 없습니다.',
            'keywords' => [],
            'categories' => [],
            'confidence' => 0,
            'extractedData' => [],
            'recommendations' => []
        ];
    }

    public function regenerateAnalysis($documentId): array
    {
        return [
            'success' => true,
            'message' => '문서 분석을 다시 시작했습니다.'
        ];
    }

    public function exportAnalysis($documentId, $format): array
    {
        return [
            'success' => true,
            'message' => "분석 결과를 {$format} 형식으로 내보냈습니다.",
            'downloadUrl' => "/downloads/analysis_{$documentId}.{$format}"
        ];
    }
}