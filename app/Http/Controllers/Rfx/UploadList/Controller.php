<?php

namespace App\Http\Controllers\Rfx\UploadList;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/rfx/uploads",
 *     tags={"RFX Upload"},
 *     summary="업로드 목록 조회",
 *     description="업로드된 파일 목록을 조회합니다.",
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         description="상태별 필터링 (pending, processing, completed, failed)",
 *         @OA\Schema(type="string", enum={"pending", "processing", "completed", "failed"})
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         required=false,
 *         description="조회할 개수 (기본값: 50)",
 *         @OA\Schema(type="integer", minimum=1, maximum=100)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="조회 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="업로드 목록 조회 성공"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="upload_id", type="string"),
 *                     @OA\Property(property="status", type="string"),
 *                     @OA\Property(property="original_filename", type="string"),
 *                     @OA\Property(property="file_size", type="string"),
 *                     @OA\Property(property="file_type", type="string"),
 *                     @OA\Property(property="created_at", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $service = new \App\Services\Rfx\UploadList\Service();
        $result = $service->execute([
            'status' => $request->query('status'),
            'limit' => $request->query('limit', 50)
        ]);

        return response()->json([
            'success' => true,
            'message' => '업로드 목록 조회 성공',
            'data' => $result
        ]);
    }
}