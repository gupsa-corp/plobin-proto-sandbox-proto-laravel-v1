<?php

namespace App\Http\Controllers\DocumentAnalysis\Create;

use App\Http\Controllers\Controller as BaseController;
use App\Services\DocumentAnalysis\Create\Service;
use Illuminate\Http\Request;

/**
 * @OA\Post(
 *     path="/api/files/{id}/analyze",
 *     tags={"Document Analysis"},
 *     summary="문서 분석 요청",
 *     description="업로드된 파일에 대해 AI 기반 문서 분석을 요청합니다.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="분석할 파일 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="분석 요청 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="문서 분석이 요청되었습니다"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=404, description="파일을 찾을 수 없음"),
 *     @OA\Response(response=422, description="유효성 검사 실패")
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request, $id)
    {
        try {
            $service = new Service();
            $result = $service->execute(['file_id' => $id]);

            return new Response($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], $e->getCode() ?: 422);
        }
    }
}