<?php

namespace App\Http\Controllers\Rfx\FileDownload;

use App\Http\Controllers\Controller as BaseController;
use App\Services\Rfx\FileDownload\Service;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @OA\Get(
 *     path="/api/rfx/file-download/{file_id}",
 *     tags={"RFX File Download"},
 *     summary="파일 다운로드",
 *     description="업로드된 파일을 다운로드합니다.",
 *     @OA\Parameter(
 *         name="file_id",
 *         in="path",
 *         required=true,
 *         description="다운로드할 파일 ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="파일 다운로드 성공",
 *         @OA\MediaType(
 *             mediaType="application/octet-stream",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="파일을 찾을 수 없음",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="파일을 찾을 수 없습니다")
 *         )
 *     )
 * )
 */
class Controller extends BaseController
{
    public function __invoke(Request $request, int $fileId): BinaryFileResponse
    {
        $service = new Service();
        return $service->downloadFile($fileId);
    }
}