<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlobinGanttSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Work Package Types 시드
        $types = [
            ['name' => 'Task', 'color' => '#3498db', 'is_milestone' => false, 'position' => 1],
            ['name' => 'Milestone', 'color' => '#e74c3c', 'is_milestone' => true, 'position' => 2],
            ['name' => 'Phase', 'color' => '#9b59b6', 'is_milestone' => false, 'position' => 3],
        ];

        foreach ($types as $type) {
            if (!DB::table('plobin_gantt_work_package_types')->where('name', $type['name'])->exists()) {
                DB::table('plobin_gantt_work_package_types')->insert(array_merge($type, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 2. Statuses 시드
        $statuses = [
            ['name' => 'New', 'color' => '#95a5a6', 'is_closed' => false, 'position' => 1],
            ['name' => 'In Progress', 'color' => '#3498db', 'is_closed' => false, 'position' => 2],
            ['name' => 'Resolved', 'color' => '#2ecc71', 'is_closed' => false, 'position' => 3],
            ['name' => 'Closed', 'color' => '#7f8c8d', 'is_closed' => true, 'position' => 4],
        ];

        foreach ($statuses as $status) {
            if (!DB::table('plobin_gantt_statuses')->where('name', $status['name'])->exists()) {
                DB::table('plobin_gantt_statuses')->insert(array_merge($status, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 3. Priorities 시드
        $priorities = [
            ['name' => 'Low', 'color' => '#95a5a6', 'is_default' => false, 'position' => 1],
            ['name' => 'Normal', 'color' => '#3498db', 'is_default' => true, 'position' => 2],
            ['name' => 'High', 'color' => '#f39c12', 'is_default' => false, 'position' => 3],
            ['name' => 'Urgent', 'color' => '#e74c3c', 'is_default' => false, 'position' => 4],
        ];

        foreach ($priorities as $priority) {
            if (!DB::table('plobin_gantt_priorities')->where('name', $priority['name'])->exists()) {
                DB::table('plobin_gantt_priorities')->insert(array_merge($priority, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 4. 비근무일 시드 (2025년 한국 공휴일)
        $nonWorkingDays = [
            ['date' => '2025-01-01', 'name' => '신정', 'recurring' => true],
            ['date' => '2025-01-28', 'name' => '설날 연휴', 'recurring' => false],
            ['date' => '2025-01-29', 'name' => '설날', 'recurring' => false],
            ['date' => '2025-01-30', 'name' => '설날 연휴', 'recurring' => false],
            ['date' => '2025-03-01', 'name' => '삼일절', 'recurring' => true],
            ['date' => '2025-05-05', 'name' => '어린이날', 'recurring' => true],
            ['date' => '2025-05-06', 'name' => '부처님오신날', 'recurring' => false],
            ['date' => '2025-06-06', 'name' => '현충일', 'recurring' => true],
            ['date' => '2025-08-15', 'name' => '광복절', 'recurring' => true],
            ['date' => '2025-10-03', 'name' => '개천절', 'recurring' => true],
            ['date' => '2025-10-06', 'name' => '추석 연휴', 'recurring' => false],
            ['date' => '2025-10-07', 'name' => '추석', 'recurring' => false],
            ['date' => '2025-10-08', 'name' => '추석 연휴', 'recurring' => false],
            ['date' => '2025-10-09', 'name' => '한글날', 'recurring' => true],
            ['date' => '2025-12-25', 'name' => '크리스마스', 'recurring' => true],
        ];

        foreach ($nonWorkingDays as $day) {
            if (!DB::table('plobin_gantt_non_working_days')->where('date', $day['date'])->exists()) {
                DB::table('plobin_gantt_non_working_days')->insert(array_merge($day, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 5. 샘플 프로젝트 생성
        $adminUser = DB::table('users')->where('email', 'test@example.com')->first();

        if ($adminUser) {
            $projectId = DB::table('plobin_gantt_projects')->insertGetId([
                'parent_id' => null,
                'identifier' => 'website-renewal',
                'name' => '웹사이트 리뉴얼 프로젝트',
                'description' => 'OpenProject 스타일의 간트차트 기능 구현을 위한 샘플 프로젝트입니다.',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'status' => 'active',
                'is_public' => false,
                'active' => true,
                'created_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. 샘플 작업 패키지 생성
            $taskTypeId = DB::table('plobin_gantt_work_package_types')->where('name', 'Task')->value('id');
            $milestoneTypeId = DB::table('plobin_gantt_work_package_types')->where('name', 'Milestone')->value('id');
            $newStatusId = DB::table('plobin_gantt_statuses')->where('name', 'New')->value('id');
            $inProgressStatusId = DB::table('plobin_gantt_statuses')->where('name', 'In Progress')->value('id');
            $normalPriorityId = DB::table('plobin_gantt_priorities')->where('name', 'Normal')->value('id');
            $highPriorityId = DB::table('plobin_gantt_priorities')->where('name', 'High')->value('id');

            // Phase 1: 기획
            $phase1Id = DB::table('plobin_gantt_work_packages')->insertGetId([
                'project_id' => $projectId,
                'parent_id' => null,
                'type_id' => $taskTypeId,
                'status_id' => $inProgressStatusId,
                'priority_id' => $highPriorityId,
                'subject' => 'Phase 1: 요구사항 분석 및 기획',
                'description' => '프로젝트 초기 기획 단계',
                'start_date' => now()->format('Y-m-d'),
                'due_date' => now()->addWeeks(2)->format('Y-m-d'),
                'estimated_hours' => 80.00,
                'done_ratio' => 50,
                'assigned_to_id' => $adminUser->id,
                'author_id' => $adminUser->id,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Task 1-1: 요구사항 정의
            $task1_1Id = DB::table('plobin_gantt_work_packages')->insertGetId([
                'project_id' => $projectId,
                'parent_id' => $phase1Id,
                'type_id' => $taskTypeId,
                'status_id' => $inProgressStatusId,
                'priority_id' => $normalPriorityId,
                'subject' => '요구사항 정의서 작성',
                'description' => '비즈니스 요구사항과 기능 명세 작성',
                'start_date' => now()->format('Y-m-d'),
                'due_date' => now()->addWeek()->format('Y-m-d'),
                'estimated_hours' => 40.00,
                'done_ratio' => 75,
                'assigned_to_id' => $adminUser->id,
                'author_id' => $adminUser->id,
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Task 1-2: UI/UX 디자인
            $task1_2Id = DB::table('plobin_gantt_work_packages')->insertGetId([
                'project_id' => $projectId,
                'parent_id' => $phase1Id,
                'type_id' => $taskTypeId,
                'status_id' => $newStatusId,
                'priority_id' => $normalPriorityId,
                'subject' => 'UI/UX 디자인 및 프로토타입',
                'description' => '화면 설계 및 사용자 경험 디자인',
                'start_date' => now()->addWeek()->format('Y-m-d'),
                'due_date' => now()->addWeeks(2)->format('Y-m-d'),
                'estimated_hours' => 40.00,
                'done_ratio' => 0,
                'assigned_to_id' => $adminUser->id,
                'author_id' => $adminUser->id,
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Milestone 1: 기획 완료
            $milestone1Id = DB::table('plobin_gantt_work_packages')->insertGetId([
                'project_id' => $projectId,
                'parent_id' => null,
                'type_id' => $milestoneTypeId,
                'status_id' => $newStatusId,
                'priority_id' => $highPriorityId,
                'subject' => 'M1: 기획 단계 완료',
                'description' => '요구사항 정의 및 디자인 승인',
                'start_date' => now()->addWeeks(2)->format('Y-m-d'),
                'due_date' => now()->addWeeks(2)->format('Y-m-d'),
                'estimated_hours' => 0,
                'done_ratio' => 0,
                'assigned_to_id' => null,
                'author_id' => $adminUser->id,
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Phase 2: 개발
            $phase2Id = DB::table('plobin_gantt_work_packages')->insertGetId([
                'project_id' => $projectId,
                'parent_id' => null,
                'type_id' => $taskTypeId,
                'status_id' => $newStatusId,
                'priority_id' => $highPriorityId,
                'subject' => 'Phase 2: 개발 및 구현',
                'description' => '백엔드 및 프론트엔드 개발',
                'start_date' => now()->addWeeks(2)->format('Y-m-d'),
                'due_date' => now()->addWeeks(8)->format('Y-m-d'),
                'estimated_hours' => 320.00,
                'done_ratio' => 0,
                'assigned_to_id' => $adminUser->id,
                'author_id' => $adminUser->id,
                'position' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 7. 의존성 관계 설정
            // Task 1-2는 Task 1-1이 완료되어야 시작 가능
            DB::table('plobin_gantt_relations')->insert([
                'from_id' => $task1_1Id,
                'to_id' => $task1_2Id,
                'relation_type' => 'precedes',
                'delay_days' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Milestone 1은 Phase 1이 완료되어야 달성 가능
            DB::table('plobin_gantt_relations')->insert([
                'from_id' => $phase1Id,
                'to_id' => $milestone1Id,
                'relation_type' => 'precedes',
                'delay_days' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Phase 2는 Milestone 1 이후 시작
            DB::table('plobin_gantt_relations')->insert([
                'from_id' => $milestone1Id,
                'to_id' => $phase2Id,
                'relation_type' => 'precedes',
                'delay_days' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
