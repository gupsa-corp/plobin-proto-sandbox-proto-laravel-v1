<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plobin\PlobinUser;
use App\Models\Plobin\UploadedFile;
use App\Models\Plobin\AnalysisRequest;
use App\Models\Plobin\DocumentAnalysis;

class PlobinRfxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 사용자 생성
        $users = [
            [
                'name' => '이분석',
                'email' => 'analyst@plobin.com',
                'role' => 'analyst',
                'department' => '데이터분석팀',
                'is_active' => true,
                'last_login_at' => now()->subHours(2)
            ],
            [
                'name' => '박검토',
                'email' => 'reviewer@plobin.com',
                'role' => 'reviewer',
                'department' => '품질관리팀',
                'is_active' => true,
                'last_login_at' => now()->subHours(1)
            ],
            [
                'name' => '최AI',
                'email' => 'ai@plobin.com',
                'role' => 'analyst',
                'department' => 'AI연구팀',
                'is_active' => true,
                'last_login_at' => now()->subMinutes(30)
            ],
            [
                'name' => '정품질',
                'email' => 'quality@plobin.com',
                'role' => 'manager',
                'department' => '품질관리팀',
                'is_active' => true,
                'last_login_at' => now()->subMinutes(15)
            ],
            [
                'name' => '김매니저',
                'email' => 'manager@plobin.com',
                'role' => 'manager',
                'department' => '기획팀',
                'is_active' => true,
                'last_login_at' => now()->subHours(3)
            ]
        ];

        foreach ($users as $userData) {
            PlobinUser::create($userData);
        }

        // 업로드된 파일 생성
        $files = [
            [
                'original_name' => '프로젝트_계획서_v2.pdf',
                'stored_name' => 'project_plan_v2_' . time() . '.pdf',
                'file_path' => 'uploads/project_plan_v2_' . time() . '.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 2419200, // 2.3MB
                'status' => 'completed',
                'uploaded_by' => 1,
                'tags' => ['계획서', '프로젝트', '2024'],
                'description' => '2024년 디지털 전환 프로젝트 계획서',
                'download_count' => 5,
                'analyzed_at' => now()->subHours(2),
                'created_at' => now()->subDays(1)
            ],
            [
                'original_name' => '데이터_분석_리포트.xlsx',
                'stored_name' => 'data_analysis_report_' . time() . '.xlsx',
                'file_path' => 'uploads/data_analysis_report_' . time() . '.xlsx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'file_size' => 1782579, // 1.7MB
                'status' => 'analyzing',
                'uploaded_by' => 2,
                'tags' => ['데이터', '분석', '리포트'],
                'description' => '분기별 데이터 분석 결과 리포트',
                'download_count' => 2,
                'analyzed_at' => null,
                'created_at' => now()->subHours(3)
            ],
            [
                'original_name' => '회의록_20241009.docx',
                'stored_name' => 'meeting_minutes_' . time() . '.docx',
                'file_path' => 'uploads/meeting_minutes_' . time() . '.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 876544, // 856KB
                'status' => 'uploaded',
                'uploaded_by' => 3,
                'tags' => ['회의록', '월간회의'],
                'description' => '10월 월간 회의록',
                'download_count' => 1,
                'analyzed_at' => null,
                'created_at' => now()->subHours(5)
            ],
            [
                'original_name' => '기술문서_API_가이드.pdf',
                'stored_name' => 'api_guide_' . time() . '.pdf',
                'file_path' => 'uploads/api_guide_' . time() . '.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 3355443, // 3.2MB
                'status' => 'completed',
                'uploaded_by' => 4,
                'tags' => ['API', '기술문서', '가이드'],
                'description' => 'RESTful API 개발 가이드 문서',
                'download_count' => 12,
                'analyzed_at' => now()->subDays(1),
                'created_at' => now()->subDays(2)
            ],
            [
                'original_name' => '사용자_매뉴얼.docx',
                'stored_name' => 'user_manual_' . time() . '.docx',
                'file_path' => 'uploads/user_manual_' . time() . '.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 1153434, // 1.1MB
                'status' => 'error',
                'uploaded_by' => 5,
                'tags' => ['매뉴얼', '사용법'],
                'description' => '시스템 사용자 매뉴얼',
                'download_count' => 0,
                'analyzed_at' => null,
                'created_at' => now()->subDays(3)
            ]
        ];

        foreach ($files as $fileData) {
            UploadedFile::create($fileData);
        }

        // 분석 요청 생성
        $requests = [
            [
                'title' => '프로젝트 계획서 분석 요청',
                'description' => '2024년 디지털 전환 프로젝트 계획서의 리스크 분석과 일정 검토가 필요합니다.',
                'status' => 'in_progress',
                'priority' => 'high',
                'requester_id' => 5,
                'assignee_id' => 1,
                'required_by' => now()->addDays(6),
                'estimated_hours' => 8,
                'completed_percentage' => 65,
                'created_at' => now()->subHours(8)
            ],
            [
                'title' => 'API 문서 품질 검토',
                'description' => '새로운 API 가이드 문서의 완성도와 누락된 내용을 확인해주세요.',
                'status' => 'pending',
                'priority' => 'medium',
                'requester_id' => 2,
                'assignee_id' => null,
                'required_by' => now()->addDays(11),
                'estimated_hours' => 4,
                'completed_percentage' => 0,
                'created_at' => now()->subHours(5)
            ],
            [
                'title' => '회의록 주요 내용 추출',
                'description' => '월간 회의록에서 액션 아이템과 결정사항을 자동으로 추출해주세요.',
                'status' => 'completed',
                'priority' => 'low',
                'requester_id' => 3,
                'assignee_id' => 1,
                'required_by' => now()->addDays(1),
                'estimated_hours' => 2,
                'completed_percentage' => 100,
                'completed_at' => now()->subHours(2),
                'created_at' => now()->subDays(1)
            ],
            [
                'title' => '사용자 매뉴얼 개선점 분석',
                'description' => '현재 사용자 매뉴얼의 가독성과 누락된 내용을 분석하여 개선점을 제시해주세요.',
                'status' => 'cancelled',
                'priority' => 'medium',
                'requester_id' => 4,
                'assignee_id' => null,
                'required_by' => now()->addDays(5),
                'estimated_hours' => 6,
                'completed_percentage' => 0,
                'cancelled_at' => now()->subDays(1),
                'cancel_reason' => '요구사항 변경으로 인한 취소',
                'created_at' => now()->subDays(2)
            ],
            [
                'title' => '데이터 분석 리포트 검증',
                'description' => '분기별 데이터 분석 리포트의 정확성과 일관성을 검증해주세요.',
                'status' => 'pending',
                'priority' => 'high',
                'requester_id' => 1,
                'assignee_id' => null,
                'required_by' => now()->addDays(3),
                'estimated_hours' => 12,
                'completed_percentage' => 0,
                'created_at' => now()->subHours(1)
            ]
        ];

        foreach ($requests as $requestData) {
            AnalysisRequest::create($requestData);
        }

        // 문서 분석 결과 생성
        $analyses = [
            [
                'file_id' => 1,
                'request_id' => 1,
                'status' => 'completed',
                'summary' => '이 문서는 2024년 프로젝트 계획서로, 주요 목표와 일정, 예산 배분에 대한 내용을 담고 있습니다.',
                'keywords' => ['프로젝트 관리', '일정 계획', '예산 편성', '리스크 관리', '성과 지표'],
                'categories' => ['계획서', '프로젝트 문서', '관리 문서'],
                'confidence_score' => 95.8,
                'extracted_data' => [
                    '프로젝트명' => '디지털 전환 프로젝트',
                    '시작일' => '2024-01-15',
                    '종료일' => '2024-12-31',
                    '예산' => '500,000,000원',
                    '담당자' => '김프로젝트'
                ],
                'recommendations' => [
                    '일정 관리를 위한 추가 체크포인트 설정 권장',
                    '리스크 대응 계획의 구체화 필요',
                    '성과 측정 지표의 명확한 정의 필요'
                ],
                'document_type' => '계획서',
                'keyword_count' => 127,
                'page_count' => 24,
                'analyzed_at' => now()->subHours(2)
            ],
            [
                'file_id' => 4,
                'request_id' => 2,
                'status' => 'completed',
                'summary' => 'API 개발 가이드라인과 사용법을 설명하는 기술 문서입니다.',
                'keywords' => ['API', 'REST', 'JSON', '인증', '예제'],
                'categories' => ['기술문서', 'API 문서', '개발 가이드'],
                'confidence_score' => 89.2,
                'extracted_data' => [
                    'API 버전' => 'v2.1',
                    '지원 형식' => 'JSON, XML',
                    '인증 방식' => 'OAuth 2.0',
                    '베이스 URL' => 'https://api.example.com/v2'
                ],
                'recommendations' => [
                    '에러 코드 설명 섹션 추가 권장',
                    '실제 사용 예제 확대 필요',
                    '버전 업데이트 내역 문서화 필요'
                ],
                'document_type' => '기술문서',
                'keyword_count' => 203,
                'page_count' => 45,
                'analyzed_at' => now()->subDays(1)
            ],
            [
                'file_id' => 2,
                'request_id' => 5,
                'status' => 'analyzing',
                'summary' => null,
                'keywords' => null,
                'categories' => null,
                'confidence_score' => null,
                'extracted_data' => null,
                'recommendations' => null,
                'document_type' => '분석 리포트',
                'keyword_count' => null,
                'page_count' => 15,
                'analyzed_at' => null
            ]
        ];

        foreach ($analyses as $analysisData) {
            DocumentAnalysis::create($analysisData);
        }

        // 분석 요청과 파일 연결
        $requestFileConnections = [
            ['analysis_request_id' => 1, 'uploaded_file_id' => 1],
            ['analysis_request_id' => 2, 'uploaded_file_id' => 4],
            ['analysis_request_id' => 3, 'uploaded_file_id' => 3],
            ['analysis_request_id' => 4, 'uploaded_file_id' => 5],
            ['analysis_request_id' => 5, 'uploaded_file_id' => 2]
        ];

        foreach ($requestFileConnections as $connection) {
            \DB::table('plobin_analysis_request_files')->insert($connection);
        }
    }
}
