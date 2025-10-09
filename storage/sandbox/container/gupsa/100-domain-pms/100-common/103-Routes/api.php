<?php

use Illuminate\Support\Facades\Route;

// 도메인별 라우트 파일 포함
require_once __DIR__ . '/../105-Routes/100-dashboard-routes.php';
require_once __DIR__ . '/../105-Routes/200-projects-routes.php';
require_once __DIR__ . '/../105-Routes/300-tasks-routes.php';
require_once __DIR__ . '/../105-Routes/400-columns-routes.php';
require_once __DIR__ . '/../105-Routes/500-user-settings-routes.php';
require_once __DIR__ . '/../105-Routes/600-permissions-routes.php';
require_once __DIR__ . '/../105-Routes/700-api-docs-routes.php';
require_once __DIR__ . '/../105-Routes/800-bookmarks-routes.php';
