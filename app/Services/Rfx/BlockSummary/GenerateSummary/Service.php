<?php

namespace App\Services\Rfx\BlockSummary\GenerateSummary;

use Illuminate\Support\Facades\Http;

class Service
{
    public function execute(array $params): array
    {
        $documentId = $params['document_id'];
        $blocks = $params['blocks'];

        if (empty($blocks)) {
            return [
                'success' => false,
                'message' => '요약할 블록이 없습니다',
                'data' => null
            ];
        }

        try {
            // 블록 텍스트 수집
            $texts = array_map(function ($block) {
                return $block['text'] ?? '';
            }, $blocks);

            // 전체 텍스트 결합
            $fullText = implode("\n", array_filter($texts));

            // AI 요약 API 호출 (OpenAI 호환 API)
            $summaryApiUrl = config('services.summary.base_url', 'http://seoul.gupsa.net:7576');

            // 모델 정보 가져오기
            $modelsResponse = Http::timeout(10)->get("{$summaryApiUrl}/v1/models");
            $modelId = 'boto'; // 기본값

            if ($modelsResponse->successful()) {
                $modelsData = $modelsResponse->json();
                if (isset($modelsData['data'][0]['id'])) {
                    $modelId = $modelsData['data'][0]['id'];
                }
            }

            // OpenAI 호환 Chat Completion API 호출
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$summaryApiUrl}/v1/chat/completions", [
                    'model' => $modelId,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '당신은 문서 요약 전문가입니다. 주어진 텍스트를 분석하여 핵심 내용을 간결하게 요약하고, 주요 포인트를 추출해주세요.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "다음 문서의 블록들을 요약해주세요. 요약과 주요 포인트를 JSON 형식으로 반환해주세요.\n\n문서 블록들:\n{$fullText}\n\n응답 형식:\n{\n  \"summary\": \"전체 요약 내용\",\n  \"key_points\": [\"포인트1\", \"포인트2\", \"포인트3\"]\n}"
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 2000,
                    'stream' => false,
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'AI 요약 API 호출에 실패했습니다: ' . ($response->json()['message'] ?? $response->body()),
                    'data' => null
                ];
            }

            $responseData = $response->json();
            $content = $responseData['choices'][0]['message']['content'] ?? '';

            // JSON 응답 파싱 시도
            $summaryData = $this->parseJsonResponse($content);

            return [
                'success' => true,
                'message' => '요약이 성공적으로 생성되었습니다',
                'data' => [
                    'summary' => $summaryData['summary'] ?? $content,
                    'key_points' => $summaryData['key_points'] ?? [],
                    'generated_at' => now()->toDateTimeString(),
                    'model_used' => $modelId,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'AI 요약 생성 중 오류가 발생했습니다: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    private function parseJsonResponse(string $content): array
    {
        // JSON 블록 추출 (```json ... ``` 또는 { ... } 형식)
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $jsonStr = $matches[1];
        } elseif (preg_match('/\{.*\}/s', $content, $matches)) {
            $jsonStr = $matches[0];
        } else {
            return [];
        }

        try {
            $decoded = json_decode($jsonStr, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        } catch (\Exception $e) {
            // JSON 파싱 실패 시 빈 배열 반환
        }

        return [];
    }
}
