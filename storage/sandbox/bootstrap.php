<?php
/**
 * Sandbox Bootstrap File
 * Laravel autoloader를 로드하여 App\Services\StorageCommon\Service를 사용할 수 있게 합니다.
 */

// Laravel autoloader 로드 (한 번만 실행)
if (!class_exists('Illuminate\Support\Facades\Facade')) {
    require_once dirname(__DIR__) . '/plobin-proto-v3/vendor/autoload.php';
}

// Laravel 환경 설정 (필요한 경우)
if (!function_exists('app_path')) {
    // Laravel 부트스트랩
    $app = require_once dirname(__DIR__) . '/plobin-proto-v3/bootstrap/app.php';
}

// StorageCommon Service 사용 준비
use App\Services\StorageCommon\Service;

// 샌드박스 환경 설정
define('SANDBOX_ROOT', __DIR__);
define('TEMPLATE_PATH', SANDBOX_ROOT . '/container/' . config('sandbox-routing.default_template'));
