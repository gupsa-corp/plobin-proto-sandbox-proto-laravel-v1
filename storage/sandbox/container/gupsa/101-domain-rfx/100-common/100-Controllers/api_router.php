<?php

// API 라우터 - 샌드박스 전용 간단한 라우팅

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// URL 파싱
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// API 경로 정규화
$apiPath = parse_url($requestUri, PHP_URL_PATH);
$pathSegments = array_filter(explode('/', $apiPath));

// 라우팅 규칙
$routes = [
    'form-submission' => [
        'controller' => 'FormSubmission',
        'methods' => ['GET', 'POST', 'DELETE']
    ]
];

try {
    // API 경로에서 리소스 추출
    $resourceKey = null;
    $action = null;
    $id = null;

    // /api/sandbox/form-submission 패턴 매칭
    foreach ($pathSegments as $index => $segment) {
        if ($segment === 'sandbox' && isset($pathSegments[$index + 1])) {
            $resourceKey = $pathSegments[$index + 1];

            // 추가 세그먼트들 처리
            if (isset($pathSegments[$index + 2])) {
                if (is_numeric($pathSegments[$index + 2])) {
                    $id = $pathSegments[$index + 2];
                } else {
                    $action = $pathSegments[$index + 2];
                }
            }
            break;
        }
    }

    // 라우트 매칭
    if (!$resourceKey || !isset($routes[$resourceKey])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'API 엔드포인트를 찾을 수 없습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $route = $routes[$resourceKey];

    // HTTP 메소드 검증
    if (!in_array($method, $route['methods'])) {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => '지원하지 않는 HTTP 메소드입니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 컨트롤러 로드
    $controllerFile = __DIR__ . '/' . $route['controller'] . '/Controller.php';

    if (!file_exists($controllerFile)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '컨트롤러를 찾을 수 없습니다.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 컨트롤러 실행
    include $controllerFile;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}