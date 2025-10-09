<?php

namespace App\Http\Controllers\FileUpload\List;

use App\Services\FileUpload\List\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/file-upload/list",
 *     tags={"File Upload"},
 *     summary="업로드된 파일 목록 조회",
 *     description="페이지네이션, 검색, 필터링이 적용된 업로드 파일 목록을 조회합니다.",
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="파일명 검색어",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="분석 상태 필터",
 *         required=false,
 *         @OA\Schema(type="string", enum={"pending", "processing", "completed", "failed"})
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="파일 타입 필터",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="페이지당 항목 수",
 *         required=false,
 *         @OA\Schema(type="integer", default=20)
 *     ),
 *     @OA\Parameter(
 *         name="offset",
 *         in="query",
 *         description="시작 위치",
 *         required=false,
 *         @OA\Schema(type="integer", default=0)
 *     ),
 *     @OA\Parameter(
 *         name="sort",
 *         in="query",
 *         description="정렬 필드",
 *         required=false,
 *         @OA\Schema(type="string", default="created_at")
 *     ),
 *     @OA\Parameter(
 *         name="direction",
 *         in="query",
 *         description="정렬 방향",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="파일 목록 조회 성공"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="files", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="total", type="integer"),
 *                 @OA\Property(property="limit", type="integer"),
 *                 @OA\Property(property="offset", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
class Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $service = new Service();
            $result = $service->execute([
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'limit' => (int) $request->get('limit', 20),
                'offset' => (int) $request->get('offset', 0),
                'sort' => $request->get('sort', 'created_at'),
                'direction' => $request->get('direction', 'desc'),
            ]);

            return response()->json([
                'success' => true,
                'message' => '파일 목록 조회 성공',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
}