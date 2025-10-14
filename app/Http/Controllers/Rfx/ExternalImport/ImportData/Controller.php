<?php

namespace App\Http\Controllers\Rfx\ExternalImport\ImportData;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Rfx\ExternalImport\ImportData\Service;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/rfx/external-import",
 *     tags={"RFX External Import"},
 *     summary="FastAPI output 데이터 가져오기",
 *     description="FastAPI에서 생성된 OCR 결과를 가져와 DB에 저장합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"request_id"},
 *             @OA\Property(
 *                 property="request_id",
 *                 type="string",
 *                 description="FastAPI에서 생성된 요청 ID",
 *                 example="0199e3bf-3572-7ea2-b7ee-2c1005cfb0e9"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="임포트 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="FastAPI 데이터 임포트가 시작되었습니다."),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="request_id", type="string", example="0199e3bf-3572-7ea2-b7ee-2c1005cfb0e9"),
 *                 @OA\Property(property="status", type="string", example="queued")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="request_id는 필수입니다.")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $service = new Service();
        $result = $service->execute($request->validated());

        return response()->json($result);
    }
}
