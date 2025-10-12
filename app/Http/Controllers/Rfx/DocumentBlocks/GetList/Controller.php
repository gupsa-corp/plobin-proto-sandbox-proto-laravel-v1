<?php

namespace App\Http\Controllers\Rfx\DocumentBlocks\GetList;

use Illuminate\Http\JsonResponse;
use App\Services\Rfx\DocumentBlocks\GetList\Service;

/**
 * @OA\Get(
 *     path="/api/rfx/documents/{id}/blocks",
 *     tags={"RFX Document Blocks"},
 *     summary="문서 블록 목록 조회",
 *     description="문서의 OCR 블록 목록을 조회합니다",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="문서 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="페이지 번호",
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Parameter(
 *         name="block_type",
 *         in="query",
 *         required=false,
 *         description="블록 타입 필터 (title, paragraph, table, list, other)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="confidence_min",
 *         in="query",
 *         required=false,
 *         description="최소 신뢰도 (0.0-1.0)",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         description="페이지당 항목 수",
 *         @OA\Schema(type="integer", default=20)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="블록 목록을 성공적으로 조회했습니다"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="문서를 찾을 수 없음")
 * )
 */
class Controller
{
    public function __invoke(Request $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $service = new Service();
        $result = $service->execute([
            'document_id' => $id,
            'page' => $validated['page'] ?? 1,
            'block_type' => $validated['block_type'] ?? null,
            'confidence_min' => $validated['confidence_min'] ?? null,
            'limit' => $validated['limit'] ?? 20,
        ]);

        return response()->json(
            new Response($result),
            $result['success'] ? 200 : 404
        );
    }
}
