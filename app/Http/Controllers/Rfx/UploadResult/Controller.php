<?php

namespace App\Http\Controllers\Rfx\UploadResult;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/rfx/upload/{uploadId}/result",
 *     tags={"RFX Upload"},
 *     summary="OCR 결과 조회",
 *     description="업로드 ID로 OCR 처리 결과를 조회합니다.",
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
 *             @OA\Property(property="message", type="string", example="OCR 결과 조회 성공"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="upload_id", type="string"),
 *                 @OA\Property(property="ocr_result", type="object"),
 *                 @OA\Property(property="created_at", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="결과 없음",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="OCR 결과를 찾을 수 없습니다")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(string $uploadId): JsonResponse
    {
        $service = new \App\Services\Rfx\UploadResult\Service();
        $result = $service->execute($uploadId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'OCR 결과를 찾을 수 없습니다'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'OCR 결과 조회 성공',
            'data' => $result
        ]);
    }
}