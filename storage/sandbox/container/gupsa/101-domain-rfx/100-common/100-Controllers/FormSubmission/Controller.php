<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class FormSubmissionController
{
    private $pdo;
    private $dbPath;

    public function __construct()
    {
        $this->dbPath = __DIR__ . '/../../200-Database/release.sqlite';
        $this->initDatabase();
    }

    private function initDatabase()
    {
        try {
            $this->pdo = new PDO("sqlite:" . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->sendError('데이터베이스 연결 실패: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 폼 제출 목록 조회
     */
    public function list()
    {
        try {
            $sql = "SELECT * FROM form_submissions ORDER BY submitted_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $submissions = $stmt->fetchAll();

            // JSON 데이터 디코딩
            foreach ($submissions as &$submission) {
                $submission['form_data'] = json_decode($submission['form_data'], true);
            }

            $this->sendSuccess($submissions);
        } catch (PDOException $e) {
            $this->sendError('데이터 조회 실패: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 단일 폼 제출 조회
     */
    public function show($id)
    {
        try {
            $sql = "SELECT * FROM form_submissions WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);

            $submission = $stmt->fetch();

            if (!$submission) {
                $this->sendError('데이터를 찾을 수 없습니다.', 404);
                return;
            }

            $submission['form_data'] = json_decode($submission['form_data'], true);
            $this->sendSuccess($submission);
        } catch (PDOException $e) {
            $this->sendError('데이터 조회 실패: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 새로운 폼 제출
     */
    public function store()
    {
        $input = $this->getJsonInput();

        // 필수 필드 검증
        if (!isset($input['form_name']) || !isset($input['form_data'])) {
            $this->sendError('필수 필드가 누락되었습니다. (form_name, form_data)', 400);
            return;
        }

        try {
            $sql = "INSERT INTO form_submissions (form_name, form_data, submitted_at, ip_address, user_agent, session_id)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $input['form_name'],
                json_encode($input['form_data']),
                date('Y-m-d H:i:s'),
                $this->getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                session_id()
            ]);

            if ($result) {
                $newId = $this->pdo->lastInsertId();
                $this->sendSuccess(['id' => $newId, 'message' => '폼이 성공적으로 제출되었습니다.']);
            } else {
                $this->sendError('폼 제출에 실패했습니다.', 500);
            }
        } catch (PDOException $e) {
            $this->sendError('폼 제출 실패: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 폼 제출 삭제
     */
    public function delete($id)
    {
        try {
            // 먼저 존재하는지 확인
            $checkSql = "SELECT id FROM form_submissions WHERE id = ?";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$id]);

            if (!$checkStmt->fetch()) {
                $this->sendError('삭제할 데이터를 찾을 수 없습니다.', 404);
                return;
            }

            // 삭제 실행
            $sql = "DELETE FROM form_submissions WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$id]);

            if ($result) {
                $this->sendSuccess(['message' => '데이터가 성공적으로 삭제되었습니다.']);
            } else {
                $this->sendError('삭제에 실패했습니다.', 500);
            }
        } catch (PDOException $e) {
            $this->sendError('삭제 실패: ' . $e->getMessage(), 500);
        }
    }

    /**
     * 폼 이름별 통계
     */
    public function stats()
    {
        try {
            $sql = "
                SELECT
                    form_name,
                    COUNT(*) as count,
                    MAX(submitted_at) as latest_submission
                FROM form_submissions
                GROUP BY form_name
                ORDER BY count DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $stats = $stmt->fetchAll();
            $this->sendSuccess($stats);
        } catch (PDOException $e) {
            $this->sendError('통계 조회 실패: ' . $e->getMessage(), 500);
        }
    }

    // 유틸리티 메소드들

    private function getJsonInput()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    private function sendSuccess($data = null, $code = 200)
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function sendError($message, $code = 400)
    {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

// API 라우팅 처리
try {
    session_start();
    $controller = new FormSubmissionController();

    $method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['REQUEST_URI'] ?? '';

    // URL에서 ID 추출
    if (preg_match('/\/(\d+)$/', $path, $matches)) {
        $id = $matches[1];
    }

    switch ($method) {
        case 'GET':
            if (isset($id)) {
                if (strpos($path, '/stats') !== false) {
                    $controller->stats();
                } else {
                    $controller->show($id);
                }
            } elseif (strpos($path, '/stats') !== false) {
                $controller->stats();
            } else {
                $controller->list();
            }
            break;

        case 'POST':
            $controller->store();
            break;

        case 'DELETE':
            if (isset($id)) {
                $controller->delete($id);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID가 필요합니다.']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => '지원하지 않는 HTTP 메소드입니다.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}