<?php

namespace App\Http\Controllers\Rfx\UploadStatus;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/rfx/upload/{uploadId}/status",
 *     tags={"RFX Upload"},
 *     summary="업로드 상태 조회",
 *     description="업로드 ID로 파일 업로드 상태를 조회합니다.",
 *     @OA\Parameter(
 *         name="uploadId",
 *         in="path",
 *         required=true,
 *         description="업로드 ID",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="조회 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="업로드 상태 조회 성공"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="upload_id", type="string"),
 *                 @OA\Property(property="status", type="string", enum={"pending", "processing", "completed", "failed"}),
 *                 @OA\Property(property="original_filename", type="string"),
 *                 @OA\Property(property="file_size", type="string"),
 *                 @OA\Property(property="file_type", type="string"),
 *                 @OA\Property(property="created_at", type="string"),
 *                 @OA\Property(property="error_message", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="업로드 정보 없음",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="업로드 정보를 찾을 수 없습니다")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(string $uploadId): JsonResponse
    {
        $service = new \App\Services\Rfx\UploadStatus\Service();
        $result = $service->execute($uploadId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => '업로드 정보를 찾을 수 없습니다'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => '업로드 상태 조회 성공',
            'data' => $result
        ]);
    }
}