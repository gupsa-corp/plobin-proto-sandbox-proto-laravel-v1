<?php

namespace App\Services\DocumentAnalysis\Create;

use App\Models\UploadedFile;
use App\Models\DocumentAsset;
use App\Models\AssetSummary;
use App\Models\SummaryVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Service
{
    public function execute(array $data): array
    {
        $fileId = $data['file_id'];
        
        $file = UploadedFile::find($fileId);
        if (!$file) {
            throw new \Exception('파일을 찾을 수 없습니다.', 404);
        }

        // 중복 분석 방지 로직
        if ($file->is_analysis_completed && $file->updated_at > now()->subHour()) {
            return [
                'id' => $fileId,
                'message' => '1시간 이내 분석 결과가 존재합니다.',
                'status' => $file->analysis_status,
                'is_cached' => true,
                'completed_at' => $file->analysis_completed_at
            ];
        }

        return DB::transaction(function () use ($file, $fileId) {
            // 분석 요청 상태로 변경
            $file->requestAnalysis();

            // AI 분석 시뮬레이션
            $analysisResult = $this->performAIAnalysis($file);
            
            // 분석 결과 검증
            $validatedResult = $this->validateAnalysisResult($analysisResult);
            
            // 문서 에셋 생성
            $assets = $this->createDocumentAssets($fileId, $validatedResult);
            
            // 분석 완료 처리
            $file->completeAnalysis('completed');

            return [
                'id' => $fileId,
                'message' => '문서 분석이 완료되었습니다.',
                'status' => 'completed',
                'result' => $validatedResult,
                'assets_created' => count($assets),
                'is_cached' => false,
                'completed_at' => $file->analysis_completed_at->format('Y-m-d H:i:s')
            ];
        });
    }

    private function performAIAnalysis(UploadedFile $file): array
    {
        $documentType = $this->detectDocumentType($file->file_path);
        $analysisType = 'general';

        $baseResult = [
            'document_type' => $documentType,
            'summary' => $this->generateSummaryByType($documentType),
            'key_points' => $this->generateKeyPoints($documentType),
            'confidence_score' => 0.95,
            'analysis_time' => now()->format('Y-m-d H:i:s'),
            'analysis_type' => $analysisType,
            'file_info' => [
                'original_name' => $file->original_name,
                'file_size' => $file->file_size,
                'mime_type' => $file->mime_type
            ]
        ];

        // 상세 분석 추가
        $baseResult['detailed_analysis'] = $this->performDetailedAnalysis($file);

        return $baseResult;
    }

    private function validateAnalysisResult(array $result): array
    {
        if (empty($result['confidence_score']) || $result['confidence_score'] < 0.5) {
            throw new \Exception('분석 결과의 신뢰도가 낮습니다.');
        }

        $requiredFields = ['document_type', 'summary', 'confidence_score'];
        foreach ($requiredFields as $field) {
            if (empty($result[$field])) {
                throw new \Exception("분석 결과에 필수 필드({$field})가 누락되었습니다.");
            }
        }

        return $result;
    }

    private function createDocumentAssets($fileId, array $analysisResult): array
    {
        $assets = [];
        
        // 기본 에셋들 생성
        $assetTemplates = [
            [
                'asset_type' => 'summary',
                'section_title' => '문서 요약',
                'content' => $analysisResult['summary'],
                'order_index' => 1
            ],
            [
                'asset_type' => 'findings',
                'section_title' => '주요 발견사항',
                'content' => implode("\n", $analysisResult['key_points']),
                'order_index' => 2
            ]
        ];

        // 문서 타입별 추가 에셋
        $additionalAssets = $this->getAdditionalAssetsByType($analysisResult['document_type']);
        $assetTemplates = array_merge($assetTemplates, $additionalAssets);

        foreach ($assetTemplates as $assetData) {
            $asset = DocumentAsset::create([
                'file_id' => $fileId,
                'asset_type' => $assetData['asset_type'],
                'section_title' => $assetData['section_title'],
                'content' => $assetData['content'],
                'order_index' => $assetData['order_index'],
                'metadata' => [
                    'confidence' => $analysisResult['confidence_score'],
                    'language' => 'ko',
                    'analysis_version' => '1.0',
                    'document_type' => $analysisResult['document_type']
                ],
                'status' => 'completed'
            ]);

            // 각 에셋에 대한 AI 요약 생성
            $summary = AssetSummary::create([
                'asset_id' => $asset->id,
                'ai_summary' => $this->generateAssetSummary($asset),
                'helpful_content' => $this->generateHelpfulContent($asset),
                'analysis_status' => 'completed',
                'analysis_metadata' => [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'confidence' => $analysisResult['confidence_score']
                ]
            ]);

            // 첫 번째 버전 생성
            SummaryVersion::create([
                'summary_id' => $summary->id,
                'version_number' => 1,
                'ai_summary' => $summary->ai_summary,
                'helpful_content' => $summary->helpful_content,
                'edit_type' => 'ai_generated',
                'edit_notes' => 'AI 자동 생성',
                'is_current' => true,
                'created_at' => now()
            ]);

            $assets[] = $asset;
        }

        return $assets;
    }

    private function detectDocumentType($filePath): string
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'pdf':
                return 'pdf_document';
            case 'docx':
            case 'doc':
                return 'word_document';
            case 'xlsx':
            case 'xls':
                return 'excel_document';
            case 'pptx':
            case 'ppt':
                return 'powerpoint_document';
            case 'txt':
                return 'text_document';
            default:
                return 'unknown_document';
        }
    }

    private function generateSummaryByType($documentType): string
    {
        $summaries = [
            'pdf_document' => 'PDF 문서 분석이 완료되었습니다. 구조화된 내용과 핵심 정보를 추출했습니다.',
            'word_document' => 'Word 문서의 텍스트 내용을 분석하여 주요 항목들을 식별했습니다.',
            'excel_document' => 'Excel 파일의 데이터를 분석하여 통계적 정보와 패턴을 도출했습니다.',
            'powerpoint_document' => 'PowerPoint 프레젠테이션의 슬라이드별 내용을 분석했습니다.',
            'text_document' => '텍스트 파일의 내용을 분석하여 핵심 정보를 요약했습니다.',
            'unknown_document' => '문서 분석이 완료되었습니다.'
        ];

        return $summaries[$documentType] ?? $summaries['unknown_document'];
    }

    private function generateKeyPoints($documentType): array
    {
        $keyPointsMap = [
            'pdf_document' => [
                '문서 구조가 체계적으로 구성됨',
                '핵심 내용이 명확히 식별됨',
                '참조 자료와 부록이 포함됨'
            ],
            'word_document' => [
                '텍스트 기반 내용 분석 완료',
                '단락별 핵심 주제 식별',
                '문서 포맷팅 정보 보존'
            ],
            'excel_document' => [
                '데이터 테이블 구조 분석',
                '수치 데이터 패턴 식별',
                '차트 및 그래프 정보 추출'
            ]
        ];

        return $keyPointsMap[$documentType] ?? [
            '문서 내용 분석 완료',
            '핵심 정보 추출됨',
            '구조화된 데이터 생성'
        ];
    }

    private function performDetailedAnalysis(UploadedFile $file): array
    {
        return [
            'word_count' => rand(500, 5000),
            'paragraph_count' => rand(10, 100),
            'sentiment_analysis' => 'neutral',
            'topics' => ['비즈니스', '기술', '프로세스'],
            'processing_time' => rand(5, 30) . '초',
            'file_size_mb' => round($file->file_size / 1024 / 1024, 2)
        ];
    }

    private function getAdditionalAssetsByType($documentType): array
    {
        switch ($documentType) {
            case 'pdf_document':
                return [
                    [
                        'asset_type' => 'technical_spec',
                        'section_title' => '기술 사양',
                        'content' => 'PDF 문서의 기술적 특성과 구조를 분석했습니다.',
                        'order_index' => 3
                    ]
                ];
                
            case 'excel_document':
                return [
                    [
                        'asset_type' => 'data_analysis',
                        'section_title' => '데이터 분석',
                        'content' => 'Excel 데이터의 통계적 분석 결과입니다.',
                        'order_index' => 3
                    ]
                ];
                
            default:
                return [
                    [
                        'asset_type' => 'conclusion',
                        'section_title' => '분석 결론',
                        'content' => '문서 분석의 주요 결과와 향후 활용 방안을 제시합니다.',
                        'order_index' => 3
                    ]
                ];
        }
    }

    private function generateAssetSummary(DocumentAsset $asset): string
    {
        $summaries = [
            'summary' => '이 섹션은 전체 문서의 핵심 내용을 요약합니다.',
            'findings' => '문서에서 발견된 주요 사실들과 중요한 정보들을 정리했습니다.',
            'technical_spec' => '문서의 기술적 요구사항과 명세를 상세히 분석했습니다.',
            'data_analysis' => '데이터 패턴과 통계적 특성을 분석한 결과입니다.',
            'conclusion' => '분석 과정에서 도출된 최종 결론과 제안사항입니다.'
        ];

        return $summaries[$asset->asset_type] ?? '이 섹션의 내용을 AI가 분석하여 요약했습니다.';
    }

    private function generateHelpfulContent(DocumentAsset $asset): string
    {
        return "이 {$asset->section_title} 섹션은 전체 문서 이해에 중요한 역할을 합니다. 
                추가적인 컨텍스트와 배경 정보를 통해 더 깊이 있는 이해가 가능합니다.";
    }
}