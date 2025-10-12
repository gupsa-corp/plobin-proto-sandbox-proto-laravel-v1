<?php

namespace App\Http\Controllers\Rfx\DocumentSections\GetList;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use App\Services\Rfx\DocumentSections\GetList\Service;
use App\Http\Controllers\Rfx\DocumentSections\GetList\Response;

/**
 * @OA\Get(
 *     path="/api/rfx/documents/{id}/sections",
 *     tags={"RFX Document Sections"},
 *     summary="문서 섹션 목록 조회",
 *     description="문서의 섹션 구조를 조회합니다 (블록을 섹션으로 그룹화)",
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
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="문서를 찾을 수 없음")
 * )
 */
class Controller
{
    public function __invoke(HttpRequest $request, string $id): JsonResponse
    {
        $service = new Service();
        $result = $service->execute($id, $request->input('page', 1));

        return response()->json(
            new Response($result),
            $result['success'] ? 200 : 404
        );
    }
}
