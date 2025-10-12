<?php

namespace App\Jobs\Rfx\DocumentAnalysis\Reanalyze;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $documentId;
    public $userId;
    public $reason;

    /**
     * Create a new job instance.
     */
    public function __construct(string $documentId, ?int $userId = null, ?string $reason = null)
    {
        $this->documentId = $documentId;
        $this->userId = $userId;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('재분석 Job 시작', [
                'documentId' => $this->documentId,
                'userId' => $this->userId
            ]);

            // 1단계: 현재 상태 스냅샷 저장
            $getResultService = new \App\Services\Rfx\DocumentAnalysis\GetResult\Service();
            $currentResult = $getResultService->execute($this->documentId);

            $saveSnapshotService = new \App\Services\Rfx\AnalysisSnapshot\SaveSnapshot\Service();
            $snapshotResult = $saveSnapshotService->execute(
                $this->documentId,
                $currentResult,
                $this->reason
            );

            if (!$snapshotResult['success']) {
                Log::error('스냅샷 저장 실패', [
                    'documentId' => $this->documentId,
                    'error' => $snapshotResult['message']
                ]);
                throw new \Exception('스냅샷 저장 실패: ' . $snapshotResult['message']);
            }

            Log::info('스냅샷 저장 성공', [
                'snapshot_id' => $snapshotResult['snapshot_id']
            ]);

            // 2단계: OCR API 재분석 요청
            $response = Http::post(config('services.ocr.base_url') . "/requests/{$this->documentId}/reanalyze");

            if (!$response->successful()) {
                Log::error('OCR API 재분석 요청 실패', [
                    'documentId' => $this->documentId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('OCR API 재분석 요청 실패');
            }

            Log::info('재분석 Job 완료', [
                'documentId' => $this->documentId
            ]);

        } catch (\Exception $e) {
            Log::error('재분석 Job 실패', [
                'documentId' => $this->documentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Job 재시도
            $this->fail($e);
        }
    }

    /**
     * Job 실패 시 처리
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('재분석 Job 최종 실패', [
            'documentId' => $this->documentId,
            'error' => $exception->getMessage()
        ]);
    }
}
