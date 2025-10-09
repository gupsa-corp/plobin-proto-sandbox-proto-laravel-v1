<?php

namespace App\Http\Controllers\Pms\Kanban;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Pms\Kanban\Service;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/pms/kanban",
 *     tags={"PMS Kanban"},
 *     summary="칸반 보드 데이터 조회",
 *     description="PMS 칸반 보드의 컬럼과 작업 데이터를 조회합니다.",
 *     @OA\Parameter(
 *         name="project_id",
 *         in="query",
 *         description="프로젝트 ID (선택사항)",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="요청이 성공적으로 처리되었습니다"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="columns", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="string", example="todo"),
 *                         @OA\Property(property="title", type="string", example="To Do"),
 *                         @OA\Property(property="color", type="string", example="#e3f2fd"),
 *                         @OA\Property(property="order", type="integer", example=1)
 *                     )
 *                 ),
 *                 @OA\Property(property="tasks", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="작업 1"),
 *                         @OA\Property(property="description", type="string", example="첫 번째 작업입니다."),
 *                         @OA\Property(property="status", type="string", example="todo"),
 *                         @OA\Property(property="assignee", type="string", example="사용자1"),
 *                         @OA\Property(property="priority", type="string", example="medium")
 *                     )
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
        $projectId = $request->get('project_id');

        $service = new Service();
        $result = $service->execute($projectId);

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