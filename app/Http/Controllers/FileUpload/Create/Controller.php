<?php

namespace App\Http\Controllers\FileUpload\Create;

use App\Http\Controllers\Controller as BaseController;
use App\Services\FileUpload\Create\Service;
use Illuminate\Http\Request as HttpRequest;

/**
 * @OA\Post(
 *     path="/api/files/upload",
 *     tags={"File Upload"},
 *     summary="파일 업로드",
 *     description="새 파일을 업로드합니다.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="file", type="file", description="업로드할 파일")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="파일 업로드 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="파일이 성공적으로 업로드되었습니다"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=422, description="유효성 검사 실패")
 * )
 */
class Controller extends BaseController
{
    public function __invoke(HttpRequest $request)
    {
        try {
            $service = new Service();
            $result = $service->execute([
                'file' => $request->file('file'),
                'options' => $request->except('file')
            ]);

            return new Response($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 422);
        }
    }
}