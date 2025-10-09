<?php

namespace App\Http\Controllers\FileUpload\Delete;

use App\Services\FileUpload\Delete\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Delete(
 *     path="/api/file-upload/delete",
 *     tags={"File Upload"},
 *     summary="업로드된 파일 삭제",
 *     description="업로드된 파일을 삭제합니다. 실제 파일과 데이터베이스 레코드가 모두 삭제됩니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"file_id"},
 *             @OA\Property(property="file_id", type="integer", description="삭제할 파일 ID", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="파일이 삭제되었습니다"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="파일 없음",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="파일을 찾을 수 없습니다"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
class Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $fileId = $request->input('file_id');
            
            if (!$fileId) {
                return response()->json([
                    'success' => false,
                    'message' => '파일 ID가 필요합니다.',
                    'data' => null
                ], 422);
            }

            $service = new Service();
            $result = $service->execute([
                'file_id' => $fileId
            ]);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => null
            ]);

        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 422;
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], $statusCode);
        }
    }
}