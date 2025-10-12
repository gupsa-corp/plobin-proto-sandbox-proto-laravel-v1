<?php

namespace App\Http\Controllers\Rfx\DocumentSections\GetDetail;

use Illuminate\Http\JsonResponse;
use App\Services\Rfx\DocumentSections\GetDetail\Service;

/**
 * @OA\Get(
 *     path="/api/rfx/documents/{id}/sections/{sectionId}",
 *     tags={"RFX Document Sections"},
 *     summary="섹션 상세 정보 조회",
 *     description="특정 섹션의 상세 정보 및 포함된 블록 목록을 조회합니다",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="문서 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="sectionId",
 *         in="path",
 *         required=true,
 *         description="섹션 ID (예: 1, 1.1, 2)",
 *         @OA\Schema(type="string")
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
 *     @OA\Response(response=404, description="섹션을 찾을 수 없음")
 * )
 */
class Controller
{
    public function __invoke(Request $request, int $id, string $sectionId): JsonResponse
    {
        $validated = $request->validated();

        $service = new Service();
        $result = $service->execute($id, $sectionId, $validated['page'] ?? 1);

        return response()->json(
            new Response($result),
            $result['success'] ? 200 : 404
        );
    }
}
