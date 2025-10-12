<?php

namespace App\Http\Controllers\Rfx\DocumentBlocks\Update;

use Illuminate\Http\JsonResponse;
use App\Services\Rfx\DocumentBlocks\Update\Service;

/**
 * @OA\Put(
 *     path="/api/rfx/documents/{id}/blocks/{blockId}",
 *     tags={"RFX Document Blocks"},
 *     summary="블록 정보 수정",
 *     description="블록의 텍스트, 타입, 신뢰도 등을 수정합니다",
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
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="text", type="string", description="블록 텍스트"),
 *             @OA\Property(property="block_type", type="string", description="블록 타입"),
 *             @OA\Property(property="confidence", type="number", description="신뢰도"),
 *             @OA\Property(property="page", type="integer", description="페이지 번호")
 *         )
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
 *     @OA\Response(response=404, description="블록을 찾을 수 없음"),
 *     @OA\Response(response=422, description="유효성 검사 실패")
 * )
 */
class Controller
{
    public function __invoke(Request $request, int $id, int $blockId): JsonResponse
    {
        $validated = $request->validated();

        $updateData = [];
        if (isset($validated['text'])) {
            $updateData['text'] = $validated['text'];
        }
        if (isset($validated['block_type'])) {
            $updateData['block_type'] = $validated['block_type'];
        }
        if (isset($validated['confidence'])) {
            $updateData['confidence'] = $validated['confidence'];
        }

        $service = new Service();
        $result = $service->execute($id, $blockId, $updateData, $validated['page'] ?? 1);

        return response()->json(
            new Response($result),
            $result['success'] ? 200 : 404
        );
    }
}
