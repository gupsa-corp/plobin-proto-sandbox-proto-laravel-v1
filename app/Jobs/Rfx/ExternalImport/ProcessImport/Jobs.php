<?php

namespace App\Jobs\Rfx\ExternalImport\ProcessImport;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\Rfx\ExternalImport\FetchFromFastApi\Service as FetchService;
use App\Services\Rfx\ExternalImport\SaveToDatabase\Service as SaveService;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected string $requestId;

    public function __construct(string $requestId)
    {
        $this->requestId = $requestId;
    }

    public function handle(): void
    {
        try {
            Log::info('FastAPI 데이터 임포트 시작', ['request_id' => $this->requestId]);

            // 1. FastAPI에서 데이터 가져오기
            $fetchService = new FetchService();
            $fastApiData = $fetchService->execute($this->requestId);

            Log::info('FastAPI 데이터 가져오기 완료', [
                'request_id' => $this->requestId,
                'total_pages' => $fastApiData['total_pages'] ?? 0
            ]);

            // 2. DB에 저장
            $saveService = new SaveService();
            $result = $saveService->execute($this->requestId, $fastApiData);

            Log::info('FastAPI 데이터 임포트 완료', [
                'request_id' => $this->requestId,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('FastAPI 데이터 임포트 실패', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
