<?php

namespace App\Http\Controllers\Pms\Projects;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Pms\Projects\Service;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/pms/projects",
 *     tags={"PMS Projects"},
 *     summary="프로젝트 목록 조회",
 *     description="PMS 프로젝트 목록을 조회합니다. 검색, 상태, 우선순위 필터링이 가능합니다.",
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="검색어 (프로젝트명, 설명)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="프로젝트 상태",
 *         required=false,
 *         @OA\Schema(type="string", enum={"active", "completed", "on_hold", "cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="priority",
 *         in="query",
 *         description="우선순위",
 *         required=false,
 *         @OA\Schema(type="string", enum={"high", "medium", "low"})
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="요청이 성공적으로 처리되었습니다"),
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="프로젝트 A"),
 *                     @OA\Property(property="description", type="string", example="첫 번째 프로젝트입니다."),
 *                     @OA\Property(property="status", type="string", example="active"),
 *                     @OA\Property(property="priority", type="string", example="high"),
 *                     @OA\Property(property="progress", type="integer", example=75)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="유효성 검사 실패")
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'priority' => $request->get('priority')
        ];

        $service = new Service();
        $result = $service->execute($filters);

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