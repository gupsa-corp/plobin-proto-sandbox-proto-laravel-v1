<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Enum 파일들 로드
require_once __DIR__ . '/../000-Config/100-enum-file-status.php';
require_once __DIR__ . '/../000-Config/101-enum-analysis-priority.php';
require_once __DIR__ . '/../000-Config/102-enum-api-endpoints.php';

/*
|--------------------------------------------------------------------------
| RFX 도메인 API Routes
|--------------------------------------------------------------------------
|
| RFX(Request for Analysis) 도메인의 API 라우트들을 정의합니다.
| 파일 업로드, 분석 요청, 문서 분석 등의 기능을 제공합니다.
|
*/

// SQLite 데이터베이스 경로
$dbPath = __DIR__ . '/../200-Database/release.sqlite';

/**
 * SQLite 연결 헬퍼 함수
 */
if (!function_exists('getRfxDbConnection')) {
    function getRfxDbConnection(): PDO {
    $dbPath = __DIR__ . '/../200-Database/release.sqlite';

    if (!file_exists($dbPath)) {
        throw new Exception("Database file not found: $dbPath");
    }

    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
    }
}

// 파일 목록 조회 API
Route::get('/files', function (Request $request) {
    try {
        $pdo = getRfxDbConnection();

        $limit = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        // 총 개수 조회 (기존 테이블 사용)
        $total = $pdo->query("SELECT COUNT(*) as count FROM uploaded_files")->fetch()['count'];

        // 파일 목록 조회 (기존 테이블 사용)
        $stmt = $pdo->prepare("SELECT * FROM uploaded_files ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $files = $stmt->fetchAll();

        $currentPage = floor($offset / $limit) + 1;
        $lastPage = ceil($total / $limit);

        return response()->json([
            'data' => $files,
            'total' => (int) $total,
            'per_page' => $limit,
            'current_page' => $currentPage,
            'last_page' => $lastPage
        ]);

    } catch (Exception $e) {
        \Log::error('RFX Files API error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => '파일 목록을 불러올 수 없습니다.',
            'debug' => $e->getMessage()
        ], 500);
    }
});

// 파일 업로드 API
Route::post('/files/upload', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => '파일 업로드 기능은 구현 예정입니다.'
    ]);
});

// 분석 요청 목록 조회 API
Route::get('/analysis-requests', function (Request $request) {
    try {
        $pdo = getRfxDbConnection();

        $limit = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        // 총 개수 조회 (tasks 테이블에서)
        $total = $pdo->query("SELECT COUNT(*) as count FROM tasks")->fetch()['count'];

        // 분석 요청 목록 조회
        $stmt = $pdo->prepare("SELECT * FROM tasks ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        $requests = $stmt->fetchAll();

        $currentPage = floor($offset / $limit) + 1;
        $lastPage = ceil($total / $limit);

        return response()->json([
            'data' => $requests,
            'total' => (int) $total,
            'per_page' => $limit,
            'current_page' => $currentPage,
            'last_page' => $lastPage
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '분석 요청 목록을 불러올 수 없습니다.'
        ], 500);
    }
});

// 통계 조회 API
Route::get('/statistics', function () {
    try {
        $pdo = getRfxDbConnection();

        $totalFiles = $pdo->query("SELECT COUNT(*) as count FROM uploaded_files")->fetch()['count'];
        $totalRequests = $pdo->query("SELECT COUNT(*) as count FROM tasks")->fetch()['count'];

        return response()->json([
            'data' => [
                'files' => [
                    'total' => (int) $totalFiles
                ],
                'requests' => [
                    'total' => (int) $totalRequests
                ]
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => '통계 정보를 불러올 수 없습니다.'
        ], 500);
    }
});

// PMS 요약 요청 목록 API
Route::get('/pms-summary-requests', function (Request $request) {
    try {
        $pdo = getRfxDbConnection();

        $limit = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        // 총 개수 조회 (테스트용 더미 데이터)
        $total = 0;

        // 빈 배열 반환
        $requests = [];

        $currentPage = floor($offset / $limit) + 1;
        $lastPage = ceil($total / $limit);

        return response()->json([
            'data' => $requests,
            'total' => (int) $total,
            'per_page' => $limit,
            'current_page' => $currentPage,
            'last_page' => $lastPage
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'PMS 요약 요청 목록을 불러올 수 없습니다.'
        ], 500);
    }
});

// PMS 요약 통계 API
Route::get('/pms-summary-statistics', function () {
    return response()->json([
        'data' => [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0
        ]
    ]);
});

// PMS 요약 요청 생성 API
Route::post('/pms-summary-request', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'PMS 요약 요청 생성 기능은 구현 예정입니다.'
    ]);
});

// PMS 요약 요청 상세 조회 API
Route::get('/pms-summary-requests/{id}', function ($id) {
    return response()->json([
        'success' => false,
        'message' => 'PMS 요약 요청을 찾을 수 없습니다.'
    ], 404);
});

// PMS 요약 요청 삭제 API
Route::delete('/pms-summary-requests/{id}', function ($id) {
    return response()->json([
        'success' => true,
        'message' => 'PMS 요약 요청 삭제 기능은 구현 예정입니다.'
    ]);
});
