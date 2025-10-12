<?php

namespace App\Jobs\Rfx\FileUpload\ProcessOcr;

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

    protected string $filePath;
    protected string $storedName;
    protected string $jobId;

    public function __construct(string $filePath, string $storedName, string $jobId)
    {
        $this->filePath = $filePath;
        $this->storedName = $storedName;
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        try {
            if (!file_exists($this->filePath)) {
                throw new \Exception("파일을 찾을 수 없습니다: {$this->filePath}");
            }

            $fileContents = file_get_contents($this->filePath);

            // 파일 확장자에 따라 적절한 엔드포인트 선택
            $extension = strtolower(pathinfo($this->storedName, PATHINFO_EXTENSION));
            $baseUrl = config('services.ocr.base_url');

            if ($extension === 'pdf') {
                $ocrUrl = $baseUrl . '/process-pdf';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'webp'])) {
                $ocrUrl = $baseUrl . '/process-image';
            } else {
                $ocrUrl = $baseUrl . '/process-document';
            }

            Log::info('OCR API 호출 시작', [
                'job_id' => $this->jobId,
                'url' => $ocrUrl,
                'file_name' => $this->storedName,
                'extension' => $extension
            ]);

            $response = Http::timeout(300)
                ->attach(
                    'file',
                    $fileContents,
                    $this->storedName
                )
                ->post($ocrUrl);

            if (!$response->successful()) {
                Log::error('OCR API 응답 에러', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                throw new \Exception("OCR API 요청 실패: HTTP {$response->status()} - {$response->body()}");
            }

            $ocrResult = $response->json();

            Log::info('OCR 처리 완료', [
                'job_id' => $this->jobId,
                'file_name' => $this->storedName,
                'ocr_result' => $ocrResult
            ]);

        } catch (\Exception $e) {
            Log::error('OCR 처리 실패', [
                'job_id' => $this->jobId,
                'file_name' => $this->storedName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
