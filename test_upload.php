<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Rfx\FileUpload\Upload\Service as UploadService;
use App\Services\Rfx\FileUpload\ProcessOcrRequest\Service as ProcessOcrRequestService;
use Illuminate\Http\UploadedFile;

echo "=== OCR 파일 업로드 테스트 ===\n\n";

// 1. 테스트 파일 경로
$testFilePath = storage_path('app/plobin/uploads/test-1page.pdf');

if (!file_exists($testFilePath)) {
    echo "❌ 테스트 파일이 존재하지 않습니다: {$testFilePath}\n";
    exit(1);
}

echo "✅ 테스트 파일 확인: {$testFilePath}\n";

// 2. 파일 업로드 (Livewire 방식 시뮬레이션)
echo "\n--- 1단계: 파일 업로드 ---\n";

$uploadService = new UploadService();

// UploadedFile 객체 생성 (실제 Livewire에서 넘어오는 형식)
$uploadedFile = new UploadedFile(
    $testFilePath,
    'test-1page.pdf',
    'application/pdf',
    null,
    true
);

$uploadResult = $uploadService->execute(['file' => $uploadedFile]);

if (!$uploadResult['success']) {
    echo "❌ 파일 업로드 실패: {$uploadResult['message']}\n";
    exit(1);
}

echo "✅ 파일 업로드 성공\n";
echo "   - ID: {$uploadResult['data']['id']}\n";
echo "   - UUID: {$uploadResult['data']['uuid']}\n";
echo "   - 파일명: {$uploadResult['data']['name']}\n";

// 3. OCR 처리 요청
echo "\n--- 2단계: OCR 처리 요청 ---\n";

$ocrService = new ProcessOcrRequestService();

$ocrResult = $ocrService->execute([
    'file_path' => $uploadResult['data']['file_path'],
    'original_name' => $uploadResult['data']['name'],
    'uploaded_file_id' => $uploadResult['data']['id']
]);

if (!$ocrResult['success']) {
    echo "❌ OCR 처리 요청 실패: {$ocrResult['message']}\n";
    exit(1);
}

echo "✅ OCR 처리 요청 성공\n";
echo "   - Job ID: {$ocrResult['data']['job_id']}\n";
echo "   - Status: {$ocrResult['data']['status']}\n";

// 4. 큐 처리 대기
echo "\n--- 3단계: 큐 처리 대기 (10초) ---\n";
sleep(10);

// 5. DB 확인
echo "\n--- 4단계: DB 확인 ---\n";

$dbRecord = DB::table('plobin_uploaded_files')
    ->where('id', $uploadResult['data']['id'])
    ->first();

if ($dbRecord) {
    echo "✅ DB 레코드 조회 성공\n";
    echo "   - ID: {$dbRecord->id}\n";
    echo "   - 파일명: {$dbRecord->original_name}\n";
    echo "   - 상태: {$dbRecord->status}\n";
    echo "   - OCR Request ID: " . ($dbRecord->ocr_request_id ?? '(없음)') . "\n";
} else {
    echo "❌ DB 레코드를 찾을 수 없습니다\n";
}

echo "\n=== 테스트 완료 ===\n";
