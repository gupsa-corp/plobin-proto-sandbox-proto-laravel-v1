<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plobin\SandboxBase;
use App\Models\Plobin\SandboxTable;
use App\Models\Plobin\SandboxField;
use App\Models\Plobin\SandboxRecord;
use App\Models\Plobin\SandboxFieldValue;
use App\Models\Plobin\SandboxFieldLink;
use App\Models\Plobin\SandboxRecordLink;
use App\Models\Plobin\SandboxView;

class SandboxPmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. 베이스 생성 - "고객 지원 티켓팅" (이미 존재하는 경우 사용)
        $base = SandboxBase::firstOrCreate([
            'slug' => 'customer-support'
        ], [
            'name' => '고객 지원 티켓팅',
            'description' => '고객 지원 요청을 관리하는 에어테이블 스타일 시스템',
            'icon' => '🎫',
            'color' => '#3B82F6',
            'created_by' => 1
        ]);

        // 2. 테이블들 생성
        
        // 티켓 테이블
        $ticketTable = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '티켓',
            'slug' => 'tickets',
            'description' => '고객 지원 티켓 관리',
            'icon' => '🎫',
            'color' => '#EF4444',
            'sort_order' => 1
        ]);

        // 고객 테이블  
        $customerTable = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '고객',
            'slug' => 'customers',
            'description' => '고객 정보 관리',
            'icon' => '👤',
            'color' => '#10B981',
            'sort_order' => 2
        ]);

        // 담당자 테이블
        $agentTable = SandboxTable::create([
            'base_id' => $base->id,
            'name' => '담당자',
            'slug' => 'agents', 
            'description' => '지원 담당자 관리',
            'icon' => '👨‍💼',
            'color' => '#8B5CF6',
            'sort_order' => 3
        ]);

        // 3. 고객 테이블 필드들
        $customerNameField = SandboxField::create([
            'table_id' => $customerTable->id,
            'name' => '회사명',
            'slug' => 'company_name',
            'field_type' => 'singleLineText',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        SandboxField::create([
            'table_id' => $customerTable->id,
            'name' => '담당자명',
            'slug' => 'contact_name',
            'field_type' => 'singleLineText',
            'sort_order' => 2
        ]);

        SandboxField::create([
            'table_id' => $customerTable->id,
            'name' => '이메일',
            'slug' => 'email',
            'field_type' => 'email',
            'sort_order' => 3
        ]);

        SandboxField::create([
            'table_id' => $customerTable->id,
            'name' => '전화번호',
            'slug' => 'phone',
            'field_type' => 'phoneNumber',
            'sort_order' => 4
        ]);

        // 4. 담당자 테이블 필드들
        $agentNameField = SandboxField::create([
            'table_id' => $agentTable->id,
            'name' => '이름',
            'slug' => 'name',
            'field_type' => 'singleLineText',
            'is_primary' => true,
            'sort_order' => 1
        ]);

        SandboxField::create([
            'table_id' => $agentTable->id,
            'name' => '부서',
            'slug' => 'department',
            'field_type' => 'singleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '기술지원', 'color' => '#3B82F6'],
                    ['name' => '영업', 'color' => '#10B981'],
                    ['name' => '마케팅', 'color' => '#F59E0B'],
                    ['name' => '개발', 'color' => '#8B5CF6']
                ]
            ],
            'sort_order' => 2
        ]);

        SandboxField::create([
            'table_id' => $agentTable->id,
            'name' => '이메일',
            'slug' => 'email',
            'field_type' => 'email',
            'sort_order' => 3
        ]);

        // 5. 티켓 테이블 필드들
        $ticketTitleField = SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '제목',
            'slug' => 'title',
            'field_type' => 'singleLineText',
            'is_required' => true,
            'is_primary' => true,
            'sort_order' => 1
        ]);

        SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '설명',
            'slug' => 'description',
            'field_type' => 'multilineText',
            'sort_order' => 2
        ]);

        SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '상태',
            'slug' => 'status',
            'field_type' => 'singleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '접수', 'color' => '#6B7280'],
                    ['name' => '진행중', 'color' => '#3B82F6'],
                    ['name' => '대기중', 'color' => '#F59E0B'],
                    ['name' => '완료', 'color' => '#10B981']
                ]
            ],
            'sort_order' => 3
        ]);

        SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '우선순위',
            'slug' => 'priority',
            'field_type' => 'singleSelect',
            'field_config' => [
                'options' => [
                    ['name' => '낮음', 'color' => '#10B981'],
                    ['name' => '보통', 'color' => '#F59E0B'],
                    ['name' => '높음', 'color' => '#EF4444'],
                    ['name' => '긴급', 'color' => '#DC2626']
                ]
            ],
            'sort_order' => 4
        ]);

        // 고객 연결 필드
        $customerLinkField = SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '고객',
            'slug' => 'customer',
            'field_type' => 'linkedRecord',
            'sort_order' => 5
        ]);

        // 담당자 연결 필드
        $agentLinkField = SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '담당자',
            'slug' => 'agent',
            'field_type' => 'linkedRecord',
            'sort_order' => 6
        ]);

        SandboxField::create([
            'table_id' => $ticketTable->id,
            'name' => '생성일',
            'slug' => 'created_at',
            'field_type' => 'createdTime',
            'sort_order' => 7
        ]);

        // 6. 관계 설정
        SandboxFieldLink::create([
            'source_field_id' => $customerLinkField->id,
            'target_table_id' => $customerTable->id,
            'link_type' => 'one_to_many'
        ]);

        SandboxFieldLink::create([
            'source_field_id' => $agentLinkField->id,
            'target_table_id' => $agentTable->id,
            'link_type' => 'one_to_many'
        ]);

        // 7. 테이블별 기본 필드 설정
        $customerTable->update(['primary_field_id' => $customerNameField->id]);
        $agentTable->update(['primary_field_id' => $agentNameField->id]);
        $ticketTable->update(['primary_field_id' => $ticketTitleField->id]);

        // 8. 샘플 데이터 생성 (레코드가 없는 경우만)
        if (SandboxRecord::where('table_id', $ticketTable->id)->count() == 0) {
            $this->createSampleData($customerTable, $agentTable, $ticketTable);
        }

        // 9. 기본 뷰 생성
        $this->createDefaultViews($ticketTable, $customerTable, $agentTable);
    }

    private function createSampleData($customerTable, $agentTable, $ticketTable)
    {
        // 고객 데이터
        $customers = [
            ['ABC 회사', '김대표', 'kim@abc.com', '02-1234-5678'],
            ['XYZ 기업', '이사장', 'lee@xyz.com', '02-2345-6789'],
            ['DEF 코퍼레이션', '박과장', 'park@def.com', '02-3456-7890']
        ];

        $customerRecords = [];
        foreach ($customers as $index => $customer) {
            $record = SandboxRecord::create([
                'table_id' => $customerTable->id,
                'record_number' => 'CUS-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'created_by' => 1
            ]);

            $fields = $customerTable->fields;
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'company_name')->first()->id,
                'value_text' => $customer[0]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'contact_name')->first()->id,
                'value_text' => $customer[1]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'email')->first()->id,
                'value_text' => $customer[2]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'phone')->first()->id,
                'value_text' => $customer[3]
            ]);

            $customerRecords[] = $record;
        }

        // 담당자 데이터
        $agents = [
            ['홍길동', '기술지원', 'hong@company.com'],
            ['김영희', '기술지원', 'kim@company.com'], 
            ['이철수', '영업', 'lee@company.com']
        ];

        $agentRecords = [];
        foreach ($agents as $index => $agent) {
            $record = SandboxRecord::create([
                'table_id' => $agentTable->id,
                'record_number' => 'AGT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'created_by' => 1
            ]);

            $fields = $agentTable->fields;
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'name')->first()->id,
                'value_text' => $agent[0]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'department')->first()->id,
                'value_json' => $agent[1]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'email')->first()->id,
                'value_text' => $agent[2]
            ]);

            $agentRecords[] = $record;
        }

        // 티켓 데이터
        $tickets = [
            ['로그인 오류 문제', '로그인이 안됩니다', '접수', '높음', 0, 0],
            ['결제 시스템 장애', '결제가 처리되지 않습니다', '진행중', '긴급', 1, 1],
            ['새 기능 요청', '대시보드 개선을 요청합니다', '대기중', '보통', 2, 2],
            ['속도 개선 요청', '페이지 로딩이 느립니다', '완료', '낮음', 0, 0]
        ];

        foreach ($tickets as $index => $ticket) {
            $record = SandboxRecord::create([
                'table_id' => $ticketTable->id,
                'record_number' => 'TKT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'created_by' => 1
            ]);

            $fields = $ticketTable->fields;
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'title')->first()->id,
                'value_text' => $ticket[0]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'description')->first()->id,
                'value_text' => $ticket[1]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'status')->first()->id,
                'value_json' => $ticket[2]
            ]);
            SandboxFieldValue::create([
                'record_id' => $record->id,
                'field_id' => $fields->where('slug', 'priority')->first()->id,
                'value_json' => $ticket[3]
            ]);
        }
    }

    private function createDefaultViews($ticketTable, $customerTable, $agentTable)
    {
        // 티켓 테이블 뷰들
        SandboxView::create([
            'table_id' => $ticketTable->id,
            'name' => '전체 티켓',
            'view_type' => 'grid',
            'is_default' => true,
            'created_by' => 1
        ]);

        SandboxView::create([
            'table_id' => $ticketTable->id,
            'name' => '상태별 칸반',
            'view_type' => 'kanban',
            'group_config' => [
                'field_slug' => 'status'
            ],
            'created_by' => 1
        ]);

        SandboxView::create([
            'table_id' => $ticketTable->id,
            'name' => '진행 중인 티켓',
            'view_type' => 'grid',
            'filter_config' => [
                [
                    'field_slug' => 'status',
                    'operator' => 'is',
                    'value' => '진행중'
                ]
            ],
            'created_by' => 1
        ]);

        // 고객 테이블 기본 뷰
        SandboxView::create([
            'table_id' => $customerTable->id,
            'name' => '모든 고객',
            'view_type' => 'grid',
            'is_default' => true,
            'created_by' => 1
        ]);

        // 담당자 테이블 기본 뷰  
        SandboxView::create([
            'table_id' => $agentTable->id,
            'name' => '모든 담당자',
            'view_type' => 'grid',
            'is_default' => true,
            'created_by' => 1
        ]);
    }
}
