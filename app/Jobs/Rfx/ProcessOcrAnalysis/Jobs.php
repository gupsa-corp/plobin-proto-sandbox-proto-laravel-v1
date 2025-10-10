<?php

namespace App\Jobs\Rfx\ProcessOcrAnalysis;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\Rfx\OcrAnalysis\ProcessWithOntology\Service as OcrOntologyService;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $requestId;
    protected string $fileId;
    protected string $filePath;

    public function __construct(int $requestId, string $fileId, string $filePath)
    {
        $this->requestId = $requestId;
        $this->fileId = $fileId;
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        try {
            // 상태를 'processing'으로 업데이트
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'processing',
                    'started_at' => now(),
                    'progress' => 10,
                ]);

            // OCR API 엔드포인트
            $ocrApiUrl = config('services.ocr.base_url');

            // Progress 업데이트: 30%
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 30]);

            // OCR API에서 기존 요청의 결과를 조회
            // fileId는 OCR API의 request_id입니다
            $response = Http::timeout(300)
                ->get("{$ocrApiUrl}/requests/{$this->fileId}");

            // Progress 업데이트: 70%
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 70]);

            if (!$response->successful()) {
                throw new \Exception("OCR API failed: " . $response->body());
            }

            $ocrResult = $response->json();

            // Progress 업데이트: 90%
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 90]);

            // 결과를 데이터베이스에 저장
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'completed',
                    'progress' => 100,
                    'result' => json_encode($ocrResult),
                    'completed_at' => now(),
                ]);

            // OCR Ontology API 호출하여 섹션 생성
            $ocrOntologyService = new OcrOntologyService();
            $ontologyResult = $ocrOntologyService->execute($this->filePath, (string)$this->requestId);

            if ($ontologyResult['success']) {
                Log::info("OCR Ontology analysis completed for request ID: {$this->requestId}", [
                    'assets_count' => $ontologyResult['assets_count'] ?? 0
                ]);
            } else {
                Log::warning("OCR Ontology analysis failed for request ID: {$this->requestId}", [
                    'error' => $ontologyResult['error'] ?? 'Unknown error'
                ]);
            }

            Log::info("OCR analysis completed for request ID: {$this->requestId}");

        } catch (\Exception $e) {
            // 오류 발생 시 상태 업데이트
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);

            Log::error("OCR analysis failed for request ID: {$this->requestId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        // Job 실패 시 추가 처리
        DB::table('rfx_ai_analysis_requests')
            ->where('id', $this->requestId)
            ->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
    }
}
