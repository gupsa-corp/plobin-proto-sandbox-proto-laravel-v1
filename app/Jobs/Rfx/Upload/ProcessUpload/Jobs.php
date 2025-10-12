<?php

namespace App\Jobs\Rfx\Upload\ProcessUpload;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected string $filePath;
    protected string $filename;
    protected string $uploadId;

    public function __construct(string $filePath, string $filename, string $uploadId)
    {
        $this->filePath = $filePath;
        $this->filename = $filename;
        $this->uploadId = $uploadId;
    }

    public function handle(): void
    {
        try {
            $ocrServiceUrl = config('services.ocr.base_url');
            $fullPath = Storage::disk('local')->path($this->filePath);

            if (!file_exists($fullPath)) {
                throw new \Exception("파일을 찾을 수 없습니다: {$fullPath}");
            }

            // OCR 서비스로 전송
            $response = Http::timeout(300)
                ->attach(
                    'file',
                    file_get_contents($fullPath),
                    $this->filename
                )
                ->post($ocrServiceUrl . '/process-image');

            if (!$response->successful()) {
                throw new \Exception("OCR 서비스 요청 실패: HTTP {$response->status()}");
            }

            $ocrResult = $response->json();

            // 결과 저장 (임시 - 추후 uploads 테이블 생성 필요)
            Log::info("파일 업로드 및 OCR 처리 완료", [
                'upload_id' => $this->uploadId,
                'filename' => $this->filename,
                'ocr_result' => $ocrResult
            ]);

        } catch (\Exception $e) {
            Log::error("파일 업로드 처리 실패", [
                'upload_id' => $this->uploadId,
                'filename' => $this->filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
