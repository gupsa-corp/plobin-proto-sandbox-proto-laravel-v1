<?php

// SQLite 데이터베이스 초기화 스크립트

$dbPath = __DIR__ . '/release.sqlite';

try {
    // PDO로 SQLite 연결
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // form_submissions 테이블 생성
    $createFormSubmissionsTable = "
        CREATE TABLE IF NOT EXISTS form_submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            form_name VARCHAR(100) NOT NULL,
            form_data TEXT NOT NULL,
            submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45),
            user_agent TEXT,
            session_id VARCHAR(100),
            user_id INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";

    $pdo->exec($createFormSubmissionsTable);

    // uploaded_files 테이블 생성
    $createUploadedFilesTable = "
        CREATE TABLE IF NOT EXISTS uploaded_files (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            file_name VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INTEGER NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            is_analysis_requested INTEGER DEFAULT 0,
            is_analysis_completed INTEGER DEFAULT 0,
            analysis_status VARCHAR(50) DEFAULT 'uploaded',
            analysis_requested_at DATETIME,
            analysis_completed_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";

    $pdo->exec($createUploadedFilesTable);

    // 인덱스 생성
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_form_name ON form_submissions(form_name)",
        "CREATE INDEX IF NOT EXISTS idx_submitted_at ON form_submissions(submitted_at)",
        "CREATE INDEX IF NOT EXISTS idx_user_id ON form_submissions(user_id)",
        "CREATE INDEX IF NOT EXISTS idx_session_id ON form_submissions(session_id)"
    ];

    foreach ($indexes as $index) {
        $pdo->exec($index);
    }

    // 샘플 데이터 삽입 (개발용)
    $sampleData = [
        [
            'form_name' => '사용자 등록',
            'form_data' => json_encode([
                'name' => '홍길동',
                'email' => 'hong@example.com',
                'phone' => '010-1234-5678',
                'message' => '가입 문의드립니다.'
            ]),
            'submitted_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'session_id' => 'sess_' . uniqid()
        ],
        [
            'form_name' => '문의하기',
            'form_data' => json_encode([
                'subject' => '서비스 이용 관련',
                'content' => '서비스 이용 중 문제가 발생했습니다.',
                'priority' => '높음'
            ]),
            'submitted_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'ip_address' => '192.168.1.101',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'session_id' => 'sess_' . uniqid()
        ],
        [
            'form_name' => '피드백',
            'form_data' => json_encode([
                'rating' => 5,
                'feedback' => '매우 만족합니다.',
                'recommend' => true,
                'category' => 'UI/UX'
            ]),
            'submitted_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'ip_address' => '192.168.1.102',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15',
            'session_id' => 'sess_' . uniqid()
        ],
        [
            'form_name' => '사용자 등록',
            'form_data' => json_encode([
                'name' => '김영희',
                'email' => 'kim@example.com',
                'phone' => '010-9876-5432',
                'message' => '회원가입 하고 싶습니다.'
            ]),
            'submitted_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'ip_address' => '192.168.1.103',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
            'session_id' => 'sess_' . uniqid()
        ],
        [
            'form_name' => '주문 취소',
            'form_data' => json_encode([
                'order_id' => 'ORD-2025-001',
                'reason' => '변심',
                'refund_method' => '신용카드',
                'additional_info' => '빠른 처리 부탁드립니다.'
            ]),
            'submitted_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            'ip_address' => '192.168.1.104',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',
            'session_id' => 'sess_' . uniqid()
        ]
    ];

    // 기존 데이터가 있는지 확인
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM form_submissions");
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    // 데이터가 없으면 샘플 데이터 삽입
    if ($count == 0) {
        $insertStmt = $pdo->prepare("
            INSERT INTO form_submissions (form_name, form_data, submitted_at, ip_address, user_agent, session_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($sampleData as $data) {
            $insertStmt->execute([
                $data['form_name'],
                $data['form_data'],
                $data['submitted_at'],
                $data['ip_address'],
                $data['user_agent'],
                $data['session_id']
            ]);
        }

        echo "샘플 데이터가 성공적으로 삽입되었습니다.\n";
    }

    echo "데이터베이스가 성공적으로 초기화되었습니다.\n";

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage() . "\n";
}
