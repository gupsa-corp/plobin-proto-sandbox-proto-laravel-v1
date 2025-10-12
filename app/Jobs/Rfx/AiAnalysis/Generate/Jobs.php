<?php

namespace App\Jobs\Rfx\AiAnalysis\Generate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Jobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $requestId;
    public $ocrRequestId;
    public $timeout = 600; // 10분

    /**
     * Create a new job instance.
     */
    public function __construct(int $requestId, string $ocrRequestId)
    {
        $this->requestId = $requestId;
        $this->ocrRequestId = $ocrRequestId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('AI 분석 Job 시작', [
                'request_id' => $this->requestId,
                'ocr_request_id' => $this->ocrRequestId
            ]);

            // 1. 상태 업데이트: processing
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'processing',
                    'started_at' => now(),
                    'progress' => 10,
                    'updated_at' => now()
                ]);

            // 2. OCR 데이터 조회
            $getResultService = new \App\Services\Rfx\DocumentAnalysis\GetResult\Service();
            $ocrResult = $getResultService->execute($this->ocrRequestId);

            if (!isset($ocrResult['ocrRawData'])) {
                throw new \Exception('OCR 데이터를 찾을 수 없습니다.');
            }

            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 30, 'updated_at' => now()]);

            // 3. OCR 텍스트 추출
            $extractedText = $this->extractTextFromOcr($ocrResult);

            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 50, 'updated_at' => now()]);

            // 4. AI 분석 수행
            $aiResult = $this->performAiAnalysis($extractedText, $ocrResult);

            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update(['progress' => 90, 'updated_at' => now()]);

            // 5. 결과 저장 및 완료
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'completed',
                    'progress' => 100,
                    'result' => json_encode($aiResult),
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            Log::info('AI 분석 Job 완료', [
                'request_id' => $this->requestId
            ]);

        } catch (\Exception $e) {
            Log::error('AI 분석 Job 실패', [
                'request_id' => $this->requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // 실패 상태 업데이트
            DB::table('rfx_ai_analysis_requests')
                ->where('id', $this->requestId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            $this->fail($e);
        }
    }

    /**
     * OCR 결과에서 텍스트 추출
     */
    private function extractTextFromOcr(array $ocrResult): string
    {
        $texts = [];

        if (isset($ocrResult['ocrRawData']['pages'])) {
            foreach ($ocrResult['ocrRawData']['pages'] as $page) {
                if (isset($page['blocks'])) {
                    foreach ($page['blocks'] as $block) {
                        if (isset($block['text'])) {
                            $texts[] = $block['text'];
                        }
                    }
                }
            }
        }

        return implode("\n", $texts);
    }

    /**
     * AI 분석 수행
     */
    private function performAiAnalysis(string $text, array $ocrResult): array
    {
        // API 키 확인
        $apiKey = env('OPENAI_API_KEY');

        if (empty($apiKey)) {
            // API 키가 없으면 기본 분석 수행
            return $this->performBasicAnalysis($text, $ocrResult);
        }

        try {
            // OpenAI API 호출
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => '당신은 문서 분석 전문가입니다. OCR로 추출된 텍스트를 분석하여 문서 유형, 주요 내용, 중요 정보를 추출해주세요. 응답은 반드시 JSON 형식으로 해주세요.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "다음 텍스트를 분석해주세요:\n\n{$text}\n\n응답 형식:\n{\n  \"document_type\": \"문서 유형\",\n  \"summary\": \"요약\",\n  \"key_info\": {\"중요정보\": \"값\"},\n  \"keywords\": [\"키워드1\", \"키워드2\"]\n}"
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $content = $result['choices'][0]['message']['content'] ?? '';

                // JSON 파싱
                $aiAnalysis = json_decode($content, true);

                if ($aiAnalysis) {
                    return array_merge($aiAnalysis, [
                        'ai_model' => 'gpt-4o-mini',
                        'confidence' => 0.85,
                        'analyzed_at' => now()->toIso8601String()
                    ]);
                }
            }

            throw new \Exception('AI API 응답 파싱 실패');

        } catch (\Exception $e) {
            Log::warning('AI API 호출 실패, 기본 분석으로 대체', [
                'error' => $e->getMessage()
            ]);

            return $this->performBasicAnalysis($text, $ocrResult);
        }
    }

    /**
     * 기본 분석 (AI API 없을 때)
     */
    private function performBasicAnalysis(string $text, array $ocrResult): array
    {
        // 간단한 키워드 추출
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function($word) {
            return mb_strlen($word) > 2;
        });
        $wordCounts = array_count_values($words);
        arsort($wordCounts);
        $keywords = array_slice(array_keys($wordCounts), 0, 10);

        // 문서 유형 추정
        $fileType = $ocrResult['ocrRawData']['file_type'] ?? 'unknown';
        $documentType = strtoupper($fileType) . ' 문서';

        // 페이지 수
        $pageCount = count($ocrResult['ocrRawData']['pages'] ?? []);

        return [
            'document_type' => $documentType,
            'summary' => "{$pageCount}페이지 분량의 {$documentType}입니다. 총 " . count($words) . "개의 단어가 추출되었습니다.",
            'key_info' => [
                'total_pages' => $pageCount,
                'word_count' => count($words),
                'file_type' => $fileType
            ],
            'keywords' => $keywords,
            'ai_model' => 'basic',
            'confidence' => 0.5,
            'analyzed_at' => now()->toIso8601String()
        ];
    }

    /**
     * Job 실패 시 처리
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AI 분석 Job 최종 실패', [
            'request_id' => $this->requestId,
            'error' => $exception->getMessage()
        ]);
    }
}
