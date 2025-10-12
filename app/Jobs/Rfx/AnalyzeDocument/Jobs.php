<?php

namespace App\Jobs\Rfx\AnalyzeDocument;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Plobin\UploadedFile;
use App\Models\Plobin\DocumentAnalysis;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 600; // 10분 타임아웃
    public $tries = 3; // 3번 재시도

    protected $fileId;

    public function __construct($fileId)
    {
        $this->fileId = $fileId;
    }

    public function handle(): void
    {
        Log::info("Starting document analysis for file ID: {$this->fileId}");

        $file = UploadedFile::find($this->fileId);
        
        if (!$file) {
            Log::error("File not found: {$this->fileId}");
            return;
        }

        try {
            // 분석 시작
            $file->update(['status' => 'analyzing']);
            
            DocumentAnalysis::updateOrCreate(
                ['file_id' => $this->fileId],
                [
                    'status' => 'analyzing',
                    'analyzed_at' => now()
                ]
            );

            // 실제 분석 로직 (시뮬레이션)
            $this->performAnalysis($file);

            Log::info("Document analysis completed for file ID: {$this->fileId}");

        } catch (\Exception $e) {
            Log::error("Document analysis failed for file ID: {$this->fileId}", [
                'error' => $e->getMessage()
            ]);
            
            // 실패 상태로 변경
            $file->update(['status' => 'error']);
            
            DocumentAnalysis::updateOrCreate(
                ['file_id' => $this->fileId],
                [
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]
            );

            throw $e;
        }
    }

    private function performAnalysis(UploadedFile $file): void
    {
        // 분석 시뮬레이션 (실제로는 여기서 AI/OCR 처리)
        sleep(10); // 10초 대기 (실제 분석 시간 시뮬레이션)
        
        $analysisResult = $this->simulateDocumentAnalysis($file);
        
        // 분석 완료 상태로 업데이트
        $file->update([
            'status' => 'completed',
            'analyzed_at' => now()
        ]);
        
        DocumentAnalysis::updateOrCreate(
            ['file_id' => $this->fileId],
            [
                'status' => 'completed',
                'summary' => $analysisResult['summary'],
                'document_type' => $analysisResult['type'],
                'confidence_score' => $analysisResult['confidence'],
                'keyword_count' => $analysisResult['keywords'],
                'page_count' => $analysisResult['pages'],
                'content' => $analysisResult['content'],
                'analyzed_at' => now()
            ]
        );
    }

    private function simulateDocumentAnalysis(UploadedFile $file): array
    {
        // 파일 확장자에 따른 시뮬레이션
        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        
        $types = [
            'pdf' => '계약서',
            'docx' => '보고서', 
            'xlsx' => '재무제표',
            'default' => '일반 문서'
        ];
        
        $documentType = $types[$extension] ?? $types['default'];
        
        return [
            'summary' => "이 {$documentType}는 AI 분석을 통해 주요 정보가 추출되었습니다. 파일명: {$file->original_name}",
            'type' => $documentType,
            'confidence' => rand(85, 98) / 100, // 0.85 ~ 0.98
            'keywords' => rand(15, 50),
            'pages' => rand(1, 20),
            'content' => "분석된 문서 내용 요약...\n\n주요 키워드: 계약, 조건, 금액, 기간\n분석 완료 시간: " . now()->format('Y-m-d H:i:s')
        ];
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Document analysis job failed for file ID: {$this->fileId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // 실패 시 상태 업데이트
        if ($file = UploadedFile::find($this->fileId)) {
            $file->update(['status' => 'error']);
            
            DocumentAnalysis::updateOrCreate(
                ['file_id' => $this->fileId],
                [
                    'status' => 'failed',
                    'error_message' => $exception->getMessage()
                ]
            );
        }
    }
}