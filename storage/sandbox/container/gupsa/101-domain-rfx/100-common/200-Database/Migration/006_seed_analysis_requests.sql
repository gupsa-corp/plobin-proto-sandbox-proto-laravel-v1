-- Phase 6: 분석 요청 샘플 데이터 시딩
-- 파일 분석 요청 관리 시스템용 테스트 데이터

-- 분석 요청 테이블 생성 (존재하지 않는 경우)
CREATE TABLE IF NOT EXISTS analysis_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    requester_name VARCHAR(100) NOT NULL,
    requester_email VARCHAR(255) NOT NULL,
    department VARCHAR(100) NULL,
    analysis_type VARCHAR(100) NOT NULL,
    description TEXT NULL,
    progress_percentage INT DEFAULT 0,
    estimated_completion DATETIME NULL,
    actual_completion DATETIME NULL,
    error_message TEXT NULL,
    result_summary TEXT NULL,
    ai_analysis_result JSON NULL,
    tags JSON NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 샘플 분석 요청 데이터 삽입
INSERT INTO analysis_requests (
    file_name, file_path, file_size, file_type, status, priority,
    requester_name, requester_email, department, analysis_type,
    description, progress_percentage, estimated_completion, actual_completion,
    error_message, result_summary, ai_analysis_result, tags, metadata, created_at, updated_at
) VALUES

-- 완료된 요청들
('AI_기술_동향_보고서_2025.pdf', '/storage/uploads/documents/AI_기술_동향_보고서_2025.pdf', 2048576, 'pdf', 'completed', 'high',
'김혁신', 'kim.innovation@company.com', '기술연구팀', '문서 요약 분석',
'2025년 AI 기술 동향에 대한 종합 분석 보고서 요약 및 핵심 인사이트 추출', 100, '2025-09-10 14:00:00', '2025-09-10 13:45:00',
NULL, 'AI 기술 발전 트렌드와 비즈니스 적용 방안에 대한 포괄적 분석 완료. 생성형 AI, 멀티모달 AI 등 주요 기술 영역별 상세 요약 제공.',
'{"sections": 15, "key_insights": 8, "recommendations": 12, "confidence_score": 0.92}',
'["AI", "기술동향", "보고서", "2025"]', '{"pages": 45, "language": "Korean", "charts": 8}', '2025-09-08 09:00:00', '2025-09-10 13:45:00'),

('스마트시티_플랫폼_제안서.docx', '/storage/uploads/documents/스마트시티_플랫폼_제안서.docx', 1536000, 'docx', 'completed', 'high',
'박도시', 'park.city@company.com', '사업개발팀', '제안서 분석',
'스마트시티 플랫폼 구축 제안서 내용 분석 및 핵심 요소 추출', 100, '2025-09-12 16:00:00', '2025-09-12 15:30:00',
NULL, '스마트시티 플랫폼의 기술 아키텍처, 예산 계획, 구현 로드맵 등 핵심 요소들이 체계적으로 분석됨. IoT, 빅데이터, AI 기술 통합 방안 제시.',
'{"sections": 12, "budget_analysis": "상세", "technical_specs": "완료", "timeline": "6개월"}',
'["스마트시티", "플랫폼", "제안서", "IoT"]', '{"pages": 28, "language": "Korean", "tables": 15}', '2025-09-10 11:00:00', '2025-09-12 15:30:00'),

('시장분석_AI솔루션_트렌드.pdf', '/storage/uploads/documents/시장분석_AI솔루션_트렌드.pdf', 3072000, 'pdf', 'completed', 'medium',
'이시장', 'lee.market@company.com', '마케팅팀', '시장 분석',
'AI 솔루션 시장 동향 및 경쟁사 분석 리포트 검토', 100, '2025-09-11 17:00:00', '2025-09-11 16:45:00',
NULL, 'AI 솔루션 시장의 성장률, 주요 플레이어, 기술 트렌드 등을 종합 분석. 시장 기회와 위험 요소들이 명확히 식별됨.',
'{"market_size": "1847억달러", "growth_rate": "37.3%", "key_players": 8, "opportunities": 5}',
'["시장분석", "AI솔루션", "경쟁사분석"]', '{"pages": 52, "language": "Korean", "graphs": 12}', '2025-09-09 14:00:00', '2025-09-11 16:45:00'),

-- 처리중인 요청들
('대규모_시스템_설계서.pdf', '/storage/uploads/documents/대규모_시스템_설계서.pdf', 4096000, 'pdf', 'processing', 'high',
'최아키', 'choi.architect@company.com', '시스템설계팀', '기술 문서 분석',
'대규모 분산 시스템 아키텍처 설계 문서 분석 및 기술 검토', 65, '2025-09-16 18:00:00', NULL,
NULL, NULL, NULL,
'["시스템설계", "아키텍처", "대규모", "분산시스템"]', '{"pages": 89, "language": "Korean", "diagrams": 25}', '2025-09-14 10:30:00', '2025-09-15 14:20:00'),

('블록체인_기술_백서.pdf', '/storage/uploads/documents/블록체인_기술_백서.pdf', 2560000, 'pdf', 'processing', 'medium',
'정블록', 'jung.blockchain@company.com', '블록체인팀', '기술 백서 분석',
'블록체인 기술 백서 내용 분석 및 핵심 기술 요소 추출', 45, '2025-09-17 15:00:00', NULL,
NULL, NULL, NULL,
'["블록체인", "기술백서", "암호화폐"]', '{"pages": 67, "language": "Korean", "technical_charts": 18}', '2025-09-13 16:15:00', '2025-09-15 11:30:00'),

-- 대기중인 요청들
('클라우드_아키텍처_가이드.pdf', '/storage/uploads/documents/클라우드_아키텍처_가이드.pdf', 1792000, 'pdf', 'pending', 'medium',
'김클라우드', 'kim.cloud@company.com', '인프라팀', '가이드 문서 분석',
'클라우드 네이티브 아키텍처 가이드 문서 검토 및 모범 사례 추출', 0, '2025-09-18 12:00:00', NULL,
NULL, NULL, NULL,
'["클라우드", "아키텍처", "가이드", "네이티브"]', '{"pages": 34, "language": "Korean", "code_examples": 22}', '2025-09-15 13:45:00', '2025-09-15 13:45:00'),

('DevOps_베스트프랙티스.pdf', '/storage/uploads/documents/DevOps_베스트프랙티스.pdf', 2304000, 'pdf', 'pending', 'low',
'이데브옵스', 'lee.devops@company.com', '개발운영팀', 'DevOps 분석',
'DevOps 베스트 프랙티스 매뉴얼 분석 및 적용 방안 검토', 0, '2025-09-19 14:00:00', NULL,
NULL, NULL, NULL,
'["DevOps", "베스트프랙티스", "CI/CD"]', '{"pages": 41, "language": "Korean", "workflows": 15}', '2025-09-15 15:20:00', '2025-09-15 15:20:00'),

-- 실패한 요청들
('손상된_문서파일.pdf', '/storage/uploads/documents/손상된_문서파일.pdf', 512000, 'pdf', 'failed', 'medium',
'한실패', 'han.failed@company.com', '품질관리팀', '문서 분석',
'품질 관리 프로세스 문서 분석', 0, '2025-09-14 10:00:00', NULL,
'파일이 손상되어 읽을 수 없습니다. PDF 구조가 깨져있어 텍스트 추출이 불가능합니다.', NULL, NULL,
'["품질관리", "프로세스", "실패"]', '{"pages": 0, "language": "Unknown", "error_type": "corrupted_file"}', '2025-09-12 09:00:00', '2025-09-14 10:15:00'),

-- 긴급 요청
('긴급_보안점검_리포트.pdf', '/storage/uploads/documents/긴급_보안점검_리포트.pdf', 1024000, 'pdf', 'pending', 'urgent',
'최보안', 'choi.security@company.com', '보안팀', '보안 분석',
'긴급 보안 점검 결과 리포트 분석 및 취약점 식별', 0, '2025-09-16 09:00:00', NULL,
NULL, NULL, NULL,
'["보안", "점검", "긴급", "취약점"]', '{"pages": 23, "language": "Korean", "security_level": "confidential"}', '2025-09-15 16:30:00', '2025-09-15 16:30:00'),

-- 다양한 파일 형식들
('사업계획서_2025.docx', '/storage/uploads/documents/사업계획서_2025.docx', 1280000, 'docx', 'completed', 'high',
'박사업', 'park.business@company.com', '경영기획팀', '사업계획 분석',
'2025년도 사업계획서 내용 분석 및 KPI 추출', 100, '2025-09-13 11:00:00', '2025-09-13 10:30:00',
NULL, '2025년 사업 목표, 전략, 예산 계획 등이 체계적으로 분석됨. 주요 KPI 및 성과 지표들이 명확히 식별됨.',
'{"goals": 8, "strategies": 12, "kpis": 15, "budget_items": 25}',
'["사업계획", "2025", "KPI", "전략"]', '{"pages": 35, "language": "Korean", "financial_tables": 8}', '2025-09-11 08:00:00', '2025-09-13 10:30:00');

-- 분석 통계 뷰 생성 (선택사항)
CREATE OR REPLACE VIEW analysis_request_stats AS
SELECT
    COUNT(*) as total_requests,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_count,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
    AVG(CASE WHEN status = 'completed' THEN progress_percentage ELSE NULL END) as avg_completion_rate,
    SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
    SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_priority_count
FROM analysis_requests;

-- 확인용 쿼리들
SELECT '분석 요청 샘플 데이터가 성공적으로 삽입되었습니다.' as message;

-- 전체 분석 요청 수 확인
SELECT COUNT(*) as total_analysis_requests FROM analysis_requests;

-- 상태별 분석 요청 수
SELECT status, COUNT(*) as count FROM analysis_requests GROUP BY status;

-- 우선순위별 분석 요청 수
SELECT priority, COUNT(*) as count FROM analysis_requests GROUP BY priority;

-- 최근 일주일 요청 수
SELECT DATE(created_at) as date, COUNT(*) as daily_requests
FROM analysis_requests
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;