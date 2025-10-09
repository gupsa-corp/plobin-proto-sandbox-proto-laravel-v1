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

$dbPath = __DIR__ . '/../200-Database/release.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // 데이터 조회
            if (isset($_GET['id'])) {
                // 단일 데이터 조회
                $stmt = $pdo->prepare("SELECT * FROM form_submissions WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $submission = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($submission) {
                    $submission['form_data'] = json_decode($submission['form_data'], true);
                    echo json_encode(['success' => true, 'data' => $submission]);
                } else {
                    echo json_encode(['success' => false, 'message' => '데이터를 찾을 수 없습니다.']);
                }
            } else {
                // 전체 데이터 조회
                $stmt = $pdo->prepare("SELECT * FROM form_submissions ORDER BY submitted_at DESC");
                $stmt->execute();
                $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($submissions as &$submission) {
                    $submission['form_data'] = json_decode($submission['form_data'], true);
                }

                echo json_encode(['success' => true, 'data' => $submissions]);
            }
            break;

        case 'POST':
            // 데이터 생성
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || !isset($input['form_name']) || !isset($input['form_data'])) {
                echo json_encode(['success' => false, 'message' => '필수 데이터가 누락되었습니다.']);
                break;
            }

            $stmt = $pdo->prepare("
                INSERT INTO form_submissions (form_name, form_data, submitted_at, ip_address, user_agent, session_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $input['form_name'],
                json_encode($input['form_data']),
                date('Y-m-d H:i:s'),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                uniqid('sess_')
            ]);

            if ($result) {
                echo json_encode(['success' => true, 'data' => ['id' => $pdo->lastInsertId()]]);
            } else {
                echo json_encode(['success' => false, 'message' => '데이터 저장에 실패했습니다.']);
            }
            break;

        case 'DELETE':
            // 데이터 삭제
            if (!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID가 필요합니다.']);
                break;
            }

            $stmt = $pdo->prepare("DELETE FROM form_submissions WHERE id = ?");
            $result = $stmt->execute([$_GET['id']]);

            if ($result && $stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => '데이터가 삭제되었습니다.']);
            } else {
                echo json_encode(['success' => false, 'message' => '삭제할 데이터를 찾을 수 없습니다.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => '지원하지 않는 메소드입니다.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '오류: ' . $e->getMessage()]);
}
?>