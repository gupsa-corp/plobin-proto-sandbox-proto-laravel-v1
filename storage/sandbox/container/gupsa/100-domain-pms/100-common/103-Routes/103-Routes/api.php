<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Enum 파일들 로드
require_once __DIR__ . '/../100-common/000-Config/100-enum-project-status.php';
require_once __DIR__ . '/../100-common/000-Config/101-enum-project-priority.php';
require_once __DIR__ . '/../100-common/000-Config/102-enum-api-endpoints.php';

/*
|--------------------------------------------------------------------------
| PMS 도메인 API Routes
|--------------------------------------------------------------------------
|
| PMS(Project Management System) 도메인의 API 라우트들을 정의합니다.
| 각 도메인은 자체 완결형으로 독립적인 API를 제공합니다.
|
*/

// SQLite 데이터베이스 경로
$dbPath = __DIR__ . '/../100-common/200-Database/release.sqlite';

/**
 * SQLite 연결 헬퍼 함수
 */
function getDbConnection(): PDO {
    global $dbPath;
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 프로젝트 테이블이 없으면 생성
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            status TEXT DEFAULT 'pending',
            priority TEXT DEFAULT 'medium',
            progress INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    return $pdo;
}

/**
 * 더미 데이터 생성 함수
 */
function seedProjectsIfEmpty(): void {
    $pdo = getDbConnection();

    // 데이터가 있는지 확인
    $count = $pdo->query("SELECT COUNT(*) as count FROM projects")->fetch()['count'];

    if ($count > 0) {
        return; // 이미 데이터가 있으면 종료
    }

    // 더미 데이터 삽입
    $dummyProjects = [
        ['웹사이트 리뉴얼', '기존 웹사이트의 UI/UX 개선', 'in_progress', 'high', 65],
        ['모바일 앱 개발', '신규 모바일 애플리케이션 개발', 'pending', 'medium', 0],
        ['데이터베이스 마이그레이션', 'MySQL에서 PostgreSQL로 이전', 'completed', 'high', 100],
        ['API 문서화', 'REST API 문서 작성 및 정리', 'on_hold', 'low', 30],
        ['성능 최적화', '서버 응답 시간 개선', 'in_progress', 'medium', 45],
        ['보안 강화', '시스템 보안 취약점 점검', 'pending', 'critical', 0],
        ['백업 시스템 구축', '자동 백업 시스템 설계', 'completed', 'high', 100],
        ['사용자 피드백 시스템', '고객 의견 수집 플랫폼', 'in_progress', 'medium', 80],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO projects (name, description, status, priority, progress, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))
    ");

    foreach ($dummyProjects as $project) {
        $stmt->execute($project);
    }
}

// 프로젝트 목록 조회 (GET)
Route::get('/projects', function (Request $request) {
    try {
        seedProjectsIfEmpty();
        $pdo = getDbConnection();

        // 필터링 및 검색 파라미터
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $priority = $request->get('priority', '');
        $limit = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        // WHERE 조건 구성
        $whereConditions = [];
        $params = [];

        if (!empty($search)) {
            $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($status) && in_array($status, ProjectStatus::all())) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
        }

        if (!empty($priority) && in_array($priority, ProjectPriority::all())) {
            $whereConditions[] = "priority = ?";
            $params[] = $priority;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // 총 개수 조회
        $countSql = "SELECT COUNT(*) as total FROM projects $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];

        // 프로젝트 목록 조회
        $sql = "SELECT * FROM projects $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $projects = $stmt->fetchAll();

        // 페이지네이션 정보 계산
        $hasNext = ($offset + $limit) < $total;
        $hasPrev = $offset > 0;

        return response()->json([
            'success' => true,
            'data' => [
                'projects' => $projects,
                'pagination' => [
                    'total' => (int) $total,
                    'offset' => $offset,
                    'limit' => $limit,
                    'hasNext' => $hasNext,
                    'hasPrev' => $hasPrev
                ]
            ]
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Projects API error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => '프로젝트 목록을 불러올 수 없습니다.'
        ], 500);
    }
});

// 프로젝트 생성 (POST)
Route::post('/projects', function (Request $request) {
    try {
        $pdo = getDbConnection();

        $name = $request->get('name');
        $description = $request->get('description', '');
        $status = $request->get('status', ProjectStatus::PENDING);
        $priority = $request->get('priority', ProjectPriority::MEDIUM);
        $progress = (int) $request->get('progress', 0);

        // 유효성 검사
        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트 이름은 필수입니다.'
            ], 400);
        }

        if (!in_array($status, ProjectStatus::all())) {
            $status = ProjectStatus::PENDING;
        }

        if (!in_array($priority, ProjectPriority::all())) {
            $priority = ProjectPriority::MEDIUM;
        }

        $progress = max(0, min(100, $progress));

        // 프로젝트 생성
        $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, status, priority, progress, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))
        ");

        $stmt->execute([$name, $description, $status, $priority, $progress]);
        $projectId = $pdo->lastInsertId();

        // 생성된 프로젝트 조회
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();

        return response()->json([
            'success' => true,
            'message' => '프로젝트가 성공적으로 생성되었습니다.',
            'data' => $project
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Project create error', [
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => '프로젝트 생성 중 오류가 발생했습니다.'
        ], 500);
    }
});

// 프로젝트 조회 (GET)
Route::get('/projects/{id}', function ($id) {
    try {
        $pdo = getDbConnection();

        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트를 찾을 수 없습니다.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $project
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Project show error', [
            'error' => $e->getMessage(),
            'project_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => '프로젝트 조회 중 오류가 발생했습니다.'
        ], 500);
    }
});

// 프로젝트 수정 (PUT)
Route::put('/projects/{id}', function (Request $request, $id) {
    try {
        $pdo = getDbConnection();

        // 프로젝트 존재 확인
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트를 찾을 수 없습니다.'
            ], 404);
        }

        // 업데이트할 필드들
        $name = $request->get('name', $project['name']);
        $description = $request->get('description', $project['description']);
        $status = $request->get('status', $project['status']);
        $priority = $request->get('priority', $project['priority']);
        $progress = (int) $request->get('progress', $project['progress']);

        // 유효성 검사
        if (empty($name)) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트 이름은 필수입니다.'
            ], 400);
        }

        if (!in_array($status, ProjectStatus::all())) {
            $status = $project['status'];
        }

        if (!in_array($priority, ProjectPriority::all())) {
            $priority = $project['priority'];
        }

        $progress = max(0, min(100, $progress));

        // 프로젝트 업데이트
        $stmt = $pdo->prepare("
            UPDATE projects
            SET name = ?, description = ?, status = ?, priority = ?, progress = ?, updated_at = datetime('now')
            WHERE id = ?
        ");

        $stmt->execute([$name, $description, $status, $priority, $progress, $id]);

        // 업데이트된 프로젝트 조회
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $updatedProject = $stmt->fetch();

        return response()->json([
            'success' => true,
            'message' => '프로젝트가 성공적으로 업데이트되었습니다.',
            'data' => $updatedProject
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Project update error', [
            'error' => $e->getMessage(),
            'project_id' => $id,
            'request_data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => '프로젝트 업데이트 중 오류가 발생했습니다.'
        ], 500);
    }
});

// 프로젝트 삭제 (DELETE)
Route::delete('/projects/{id}', function ($id) {
    try {
        $pdo = getDbConnection();

        // 프로젝트 존재 확인
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => '프로젝트를 찾을 수 없습니다.'
            ], 404);
        }

        // 프로젝트 삭제
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);

        return response()->json([
            'success' => true,
            'message' => '프로젝트가 성공적으로 삭제되었습니다.'
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Project delete error', [
            'error' => $e->getMessage(),
            'project_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => '프로젝트 삭제 중 오류가 발생했습니다.'
        ], 500);
    }
});

// 프로젝트 통계 조회 (GET)
Route::get('/statistics', function () {
    try {
        seedProjectsIfEmpty();
        $pdo = getDbConnection();

        // 전체 통계
        $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
        $total = $totalStmt->fetch()['total'];

        // 상태별 통계
        $statusStats = [];
        foreach (ProjectStatus::all() as $status) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects WHERE status = ?");
            $stmt->execute([$status]);
            $statusStats[$status] = $stmt->fetch()['count'];
        }

        // 우선순위별 통계
        $priorityStats = [];
        foreach (ProjectPriority::all() as $priority) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects WHERE priority = ?");
            $stmt->execute([$priority]);
            $priorityStats[$priority] = $stmt->fetch()['count'];
        }

        // 평균 진행률
        $avgStmt = $pdo->query("SELECT AVG(progress) as avg_progress FROM projects");
        $avgProgress = round($avgStmt->fetch()['avg_progress'], 1);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => (int) $total,
                'status' => $statusStats,
                'priority' => $priorityStats,
                'avg_progress' => $avgProgress
            ]
        ]);

    } catch (Exception $e) {
        \Log::error('PMS Statistics error', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => '통계 정보를 불러올 수 없습니다.'
        ], 500);
    }
});