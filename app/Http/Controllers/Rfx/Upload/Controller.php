<?php

namespace App\Http\Controllers\Rfx\Upload;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/rfx/upload",
 *     tags={"RFX Upload"},
 *     summary="RFX 파일 업로드",
 *     description="RFX 관련 파일을 업로드하고 OCR 서비스로 전송합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="업로드할 파일"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="업로드 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="파일이 성공적으로 업로드되었습니다"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="upload_id", type="string", example="upload_12345")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="유효성 검사에 실패했습니다"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(\App\Http\Controllers\Rfx\Upload\Request $request): JsonResponse
    {
        $service = new \App\Services\Rfx\Upload\Service();
        $result = $service->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => '파일이 성공적으로 업로드되었습니다',
            'data' => $result
        ]);
    }
}