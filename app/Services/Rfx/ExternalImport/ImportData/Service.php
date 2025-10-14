<?php

namespace App\Services\Rfx\ExternalImport\ImportData;

use App\Jobs\Rfx\ExternalImport\ProcessImport\Jobs as ProcessImportJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Rfx\ExternalImport\ImportData\Response;

class Service
{
    public function execute(array $data): array
    {
        try {
            $requestId = $data['request_id'];

            // 이미 임포트된 데이터인지 확인
            $existing = DB::table('rfx_external_imports')
                ->where('request_id', $requestId)
                ->first();

            if ($existing && $existing->status === 'completed') {
                return Response::error('이미 임포트된 데이터입니다.');
            }

            // rfx_external_imports 테이블에 pending 상태로 기록
            DB::table('rfx_external_imports')->updateOrInsert(
                ['request_id' => $requestId],
                [
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // 큐에 임포트 작업 등록
            ProcessImportJob::dispatch($requestId);

            return Response::success([
                'request_id' => $requestId,
                'status' => 'queued'
            ]);

        } catch (\Exception $e) {
            return Response::error('임포트 요청 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
}
