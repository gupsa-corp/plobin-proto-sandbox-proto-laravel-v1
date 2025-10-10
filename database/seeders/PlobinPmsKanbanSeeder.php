<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pms\Project;
use Carbon\Carbon;

class PlobinPmsKanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 중국집 음식 메뉴 컨셉의 프로젝트 데이터
        $projects = [
            // Planning (계획 중) - 2개
            [
                'title' => '짜장면 레시피 개선 프로젝트',
                'description' => '고객 피드백을 반영한 짜장면 소스 농도 조절 및 춘장 배합 비율 재설정',
                'status' => 'planning',
                'assignee' => '주방장 김철수',
                'priority' => 'high',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(14),
                'due_date' => Carbon::now()->addDays(14),
                'tags' => ['메뉴개선', '인기메뉴', '긴급'],
                'progress' => 0,
            ],
            [
                'title' => '탕수육 바삭함 유지 연구',
                'description' => '배달 시에도 바삭함을 유지할 수 있는 새로운 튀김옷 개발 및 포장 방법 연구',
                'status' => 'planning',
                'assignee' => '연구개발팀 박영희',
                'priority' => 'medium',
                'start_date' => Carbon::now()->addDays(3),
                'end_date' => Carbon::now()->addDays(21),
                'due_date' => Carbon::now()->addDays(21),
                'tags' => ['연구개발', '배달', '품질개선'],
                'progress' => 0,
            ],

            // In Progress (진행 중) - 3개
            [
                'title' => '짬뽕 국물 레시피 표준화',
                'description' => '매장별 맛 차이를 줄이기 위한 짬뽕 국물 레시피 표준화 작업 진행 중',
                'status' => 'in_progress',
                'assignee' => '주방장 이수진',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(7),
                'due_date' => Carbon::now()->addDays(7),
                'tags' => ['표준화', '품질관리', '중요'],
                'progress' => 45,
            ],
            [
                'title' => '우동 면발 굵기 조정',
                'description' => '고객 선호도 조사 결과 반영하여 우동 면발 굵기를 2mm에서 2.5mm로 조정',
                'status' => 'in_progress',
                'assignee' => '제면팀 최민수',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(3),
                'end_date' => Carbon::now()->addDays(10),
                'due_date' => Carbon::now()->addDays(10),
                'tags' => ['메뉴개선', '고객피드백'],
                'progress' => 60,
            ],
            [
                'title' => '볶음밥 야채 배합 비율 조정',
                'description' => '볶음밥의 야채 신선도와 배합 비율 개선으로 맛과 영양 균형 맞추기',
                'status' => 'in_progress',
                'assignee' => '주방장 김철수',
                'priority' => 'low',
                'start_date' => Carbon::now()->subDays(7),
                'end_date' => Carbon::now()->addDays(15),
                'due_date' => Carbon::now()->addDays(15),
                'tags' => ['메뉴개선', '건강'],
                'progress' => 30,
            ],

            // Review (검토 중) - 3개
            [
                'title' => '깐풍기 소스 매운맛 단계화',
                'description' => '깐풍기 소스를 순한맛, 보통맛, 매운맛 3단계로 구분하여 제공',
                'status' => 'review',
                'assignee' => '주방장 박영희',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(5),
                'due_date' => Carbon::now()->addDays(5),
                'tags' => ['메뉴다양화', '고객맞춤'],
                'progress' => 85,
            ],
            [
                'title' => '양장피 소스 리뉴얼',
                'description' => '겨자 소스와 간장 소스의 배합 비율 조정 및 새로운 참기름 향 추가',
                'status' => 'review',
                'assignee' => '연구개발팀 이수진',
                'priority' => 'low',
                'start_date' => Carbon::now()->subDays(12),
                'end_date' => Carbon::now()->addDays(3),
                'due_date' => Carbon::now()->addDays(3),
                'tags' => ['리뉴얼', '메뉴개선'],
                'progress' => 90,
            ],
            [
                'title' => '군만두 크기 표준화',
                'description' => '군만두 크기를 일정하게 유지하기 위한 제조 프로세스 개선',
                'status' => 'review',
                'assignee' => '제조팀 최민수',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(14),
                'end_date' => Carbon::now()->addDays(2),
                'due_date' => Carbon::now()->addDays(2),
                'tags' => ['표준화', '품질관리'],
                'progress' => 95,
            ],

            // Completed (완료) - 2개
            [
                'title' => '중화비빔밥 신메뉴 출시',
                'description' => '짜장, 짬뽕 소스를 활용한 중화비빔밥 신메뉴 개발 및 테스트 완료',
                'status' => 'completed',
                'assignee' => '주방장 김철수',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->subDays(3),
                'due_date' => Carbon::now()->subDays(3),
                'tags' => ['신메뉴', '출시완료'],
                'progress' => 100,
            ],
            [
                'title' => '마파두부 나트륨 함량 감소',
                'description' => '건강을 고려한 저염 마파두부 레시피 개발 및 적용 완료',
                'status' => 'completed',
                'assignee' => '연구개발팀 박영희',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(7),
                'due_date' => Carbon::now()->subDays(7),
                'tags' => ['건강', '저염', '완료'],
                'progress' => 100,
            ],
        ];

        // 기존 데이터 삭제 후 새로 생성
        Project::truncate();

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
