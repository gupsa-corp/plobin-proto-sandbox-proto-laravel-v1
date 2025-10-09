<?php

namespace App\Http\Controllers\Pms\Dashboard;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Pms\Dashboard\Service;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/pms/dashboard",
 *     tags={"PMS Dashboard"},
 *     summary="PMS 대시보드 데이터 조회",
 *     description="PMS 프로젝트 관리 시스템의 대시보드 데이터를 조회합니다.",
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="요청이 성공적으로 처리되었습니다"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="total_projects", type="integer", example=25),
 *                 @OA\Property(property="active_projects", type="integer", example=18),
 *                 @OA\Property(property="completed_projects", type="integer", example=7)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="유효성 검사 실패"),
 *     @OA\Response(response=500, description="서버 오류")
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request)
    {
        $service = new Service();
        $result = $service->execute();

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => '요청이 성공적으로 처리되었습니다',
                'data' => $result['data']
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => null
        ], 422);
    }
}