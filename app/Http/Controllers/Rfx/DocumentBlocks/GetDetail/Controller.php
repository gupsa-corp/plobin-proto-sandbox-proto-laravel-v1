<?php

namespace App\Http\Controllers\Rfx\DocumentBlocks\GetDetail;

use Illuminate\Http\JsonResponse;
use App\Services\Rfx\DocumentBlocks\GetDetail\Service;

/**
 * @OA\Get(
 *     path="/api/rfx/documents/{id}/blocks/{blockId}",
 *     tags={"RFX Document Blocks"},
 *     summary="블록 상세 정보 조회",
 *     description="특정 블록의 상세 정보를 조회합니다",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="문서 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="blockId",
 *         in="path",
 *         required=true,
 *         description="블록 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="페이지 번호",
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="블록을 찾을 수 없음")
 * )
 */
class Controller
{
    public function __invoke(Request $request, int $id, int $blockId): JsonResponse
    {
        $validated = $request->validated();

        $service = new Service();
        $result = $service->execute($id, $blockId, $validated['page'] ?? 1);

        return response()->json(
            new Response($result),
            $result['success'] ? 200 : 404
        );
    }
}
