<?php

namespace App\Http\Controllers\Rfx\FileUpload;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Rfx\FileUpload\Upload\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Post(
 *     path="/api/rfx/file-upload",
 *     tags={"RFX File Upload"},
 *     summary="파일 업로드",
 *     description="RFX 시스템에 파일을 업로드합니다.",
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
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string",
 *                     description="파일 설명"
 *                 ),
 *                 @OA\Property(
 *                     property="tags",
 *                     type="array",
 *                     @OA\Items(type="string"),
 *                     description="파일 태그"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="파일 업로드 성공",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="파일이 성공적으로 업로드되었습니다"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="file_id", type="integer", example=1),
 *                 @OA\Property(property="original_name", type="string", example="document.pdf"),
 *                 @OA\Property(property="file_size", type="integer", example=1024000)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="유효성 검사 실패",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="파일 업로드에 실패했습니다")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $service = new Service();
        $result = $service->execute($request->all());
        
        return response()->json($result);
    }
}