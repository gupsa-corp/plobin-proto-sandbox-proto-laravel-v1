<?php

namespace App\Http\Controllers\FormSubmission\Create;

use App\Http\Controllers\Controller as BaseController;
use App\Services\FormSubmission\Create\Service;

/**
 * @OA\Post(
 *     path="/api/form-submissions",
 *     tags={"Form Submission"},
 *     summary="폼 제출",
 *     description="새로운 폼 데이터를 제출합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_name", "form_data"},
 *             @OA\Property(property="form_name", type="string", description="폼 이름"),
 *             @OA\Property(property="form_data", type="object", description="폼 데이터")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="폼 제출 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="폼이 성공적으로 제출되었습니다"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=422, description="유효성 검사 실패")
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $service = new Service();
            $result = $service->execute([
                'form_name' => $request->input('form_name'),
                'form_data' => $request->input('form_data'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId()
            ]);

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