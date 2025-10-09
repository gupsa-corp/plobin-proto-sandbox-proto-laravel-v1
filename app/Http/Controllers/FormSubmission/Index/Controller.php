<?php

namespace App\Http\Controllers\FormSubmission\Index;

use App\Http\Controllers\Controller as BaseController;
use App\Services\FormSubmission\Index\Service;

/**
 * @OA\Get(
 *     path="/api/form-submissions",
 *     tags={"Form Submission"},
 *     summary="폼 제출 목록 조회",
 *     description="모든 폼 제출 데이터를 조회합니다.",
 *     @OA\Response(
 *         response=200,
 *         description="폼 제출 목록 조회 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="폼 제출 목록을 조회했습니다"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke()
    {
        try {
            $service = new Service();
            $result = $service->execute([]);

            return new Response($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}