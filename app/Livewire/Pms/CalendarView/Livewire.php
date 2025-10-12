<?php

namespace App\Livewire\Pms\CalendarView;

use Livewire\Component;
use App\Services\Pms\GetCalendarRequests\Service;
use Carbon\Carbon;

class Livewire extends Component
{
    public $requests = [];
    public $calendarEvents = [];
    public $currentDate;
    public $viewMode = 'month';
    public $selectedDate = null;
    public $showCreateModal = false;
    public $eventForm = [
        'title' => '',
        'description' => '',
        'start_date' => '',
        'end_date' => '',
        'estimated_hours' => null,
        'priority' => 'medium'
    ];
    public $filterPriority = '';
    public $filterStatus = '';
    public $showFilters = false;
    public $selectedEventId = null;
    public $showEventDetailModal = false;
    public $editMode = false;
    public $editForm = [
        'title' => '',
        'description' => '',
        'start_date' => '',
        'end_date' => '',
        'estimated_hours' => null,
        'priority' => 'medium',
        'status' => 'pending',
        'completed_percentage' => 0
    ];

    public function mount()
    {
        $this->currentDate = now()->format('Y-m-d');
        $this->loadCalendarData();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->loadCalendarData();
    }

    public function previousPeriod()
    {
        if ($this->viewMode === 'month') {
            $this->currentDate = now()->parse($this->currentDate)->subMonth()->format('Y-m-d');
        } else {
            $this->currentDate = now()->parse($this->currentDate)->subWeek()->format('Y-m-d');
        }
        $this->loadCalendarData();
    }

    public function nextPeriod()
    {
        if ($this->viewMode === 'month') {
            $this->currentDate = now()->parse($this->currentDate)->addMonth()->format('Y-m-d');
        } else {
            $this->currentDate = now()->parse($this->currentDate)->addWeek()->format('Y-m-d');
        }
        $this->loadCalendarData();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function goToToday()
    {
        $this->currentDate = now()->format('Y-m-d');
        $this->loadCalendarData();
    }

    public function openCreateModal($date = null)
    {
        \Log::info('openCreateModal called with date: ' . ($date ?: 'null'));
        $selectedDate = $date ?: $this->selectedDate ?: $this->currentDate;
        $this->eventForm['start_date'] = $selectedDate;
        $this->eventForm['end_date'] = $selectedDate;
        $this->showCreateModal = true;
        \Log::info('showCreateModal set to: ' . ($this->showCreateModal ? 'true' : 'false'));
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetEventForm();
    }

    public function resetEventForm()
    {
        $this->eventForm = [
            'title' => '',
            'description' => '',
            'start_date' => '',
            'end_date' => '',
            'estimated_hours' => null,
            'priority' => 'medium'
        ];
    }

    public function createEvent()
    {
        $this->validate([
            'eventForm.title' => 'required|min:3',
            'eventForm.description' => 'nullable|min:10',
            'eventForm.start_date' => 'nullable|date',
            'eventForm.end_date' => 'nullable|date|after_or_equal:eventForm.start_date',
            'eventForm.estimated_hours' => 'nullable|numeric|min:1|max:1000'
        ], [
            'eventForm.title.required' => '제목을 입력해주세요.',
            'eventForm.title.min' => '제목은 3자 이상이어야 합니다.',
            'eventForm.description.min' => '설명은 10자 이상이어야 합니다.',
            'eventForm.end_date.after_or_equal' => '종료일은 시작일 이후여야 합니다.',
            'eventForm.estimated_hours.min' => '최소 1시간 이상이어야 합니다.',
            'eventForm.estimated_hours.max' => '최대 1000시간까지 가능합니다.'
        ]);

        // 분석 요청 생성 서비스 호출
        $createService = new \App\Services\Pms\CreateAnalysisRequest\Service();
        $result = $createService->execute([
            'title' => $this->eventForm['title'],
            'description' => $this->eventForm['description'],
            'start_date' => $this->eventForm['start_date'],
            'end_date' => $this->eventForm['end_date'],
            'estimated_hours' => $this->eventForm['estimated_hours'],
            'priority' => $this->eventForm['priority'],
            'requester_id' => auth()->id() ?? 1, // 임시로 1번 사용자
        ]);

        if ($result['success']) {
            session()->flash('message', '분석 요청이 성공적으로 생성되었습니다.');
            $this->closeCreateModal();
            $this->loadCalendarData();
        } else {
            session()->flash('error', $result['message'] ?? '요청 생성에 실패했습니다.');
        }
    }

    public function clearFilters()
    {
        $this->filterPriority = '';
        $this->filterStatus = '';
        $this->loadCalendarData();
    }

    public function showEventDetail($eventId)
    {
        $this->selectedEventId = $eventId;
        $this->showEventDetailModal = true;

        // 즉시 수정 모드로 진입
        $this->enableEditMode();
    }

    public function closeEventDetailModal()
    {
        $this->selectedEventId = null;
        $this->showEventDetailModal = false;
        $this->editMode = false;
        $this->resetEditForm();
    }

    public function getSelectedEvent()
    {
        if (!$this->selectedEventId) {
            return null;
        }

        foreach ($this->requests as $event) {
            if ($event['id'] == $this->selectedEventId) {
                return $event;
            }
        }

        return null;
    }

    public function enableEditMode()
    {
        $selectedEvent = $this->getSelectedEvent();
        if ($selectedEvent) {
            $this->editForm = [
                'title' => $selectedEvent['title'],
                'description' => $selectedEvent['description'],
                'start_date' => $selectedEvent['start_date'],
                'end_date' => $selectedEvent['end_date'],
                'estimated_hours' => $selectedEvent['estimated_hours'],
                'priority' => $selectedEvent['priority'],
                'status' => $selectedEvent['status'],
                'completed_percentage' => $selectedEvent['completed_percentage']
            ];
            $this->editMode = true;
        }
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->resetEditForm();
    }

    public function resetEditForm()
    {
        $this->editForm = [
            'title' => '',
            'description' => '',
            'start_date' => '',
            'end_date' => '',
            'estimated_hours' => null,
            'priority' => 'medium',
            'status' => 'pending',
            'completed_percentage' => 0
        ];
    }

    public function updateEvent()
    {
        $this->validate([
            'editForm.title' => 'required|min:3',
            'editForm.description' => 'nullable|min:10',
            'editForm.start_date' => 'nullable|date',
            'editForm.end_date' => 'nullable|date|after_or_equal:editForm.start_date',
            'editForm.estimated_hours' => 'nullable|numeric|min:1|max:1000',
            'editForm.completed_percentage' => 'nullable|numeric|min:0|max:100'
        ], [
            'editForm.title.required' => '제목을 입력해주세요.',
            'editForm.title.min' => '제목은 3자 이상이어야 합니다.',
            'editForm.description.min' => '설명은 10자 이상이어야 합니다.',
            'editForm.end_date.after_or_equal' => '종료일은 시작일 이후여야 합니다.',
            'editForm.estimated_hours.min' => '최소 1시간 이상이어야 합니다.',
            'editForm.estimated_hours.max' => '최대 1000시간까지 가능합니다.',
            'editForm.completed_percentage.min' => '진행률은 0% 이상이어야 합니다.',
            'editForm.completed_percentage.max' => '진행률은 100% 이하여야 합니다.'
        ]);

        // 프로젝트 수정 서비스 호출
        $updateService = new \App\Services\Pms\UpdateProject\Service();
        $result = $updateService->execute([
            'id' => $this->selectedEventId,
            'title' => $this->editForm['title'],
            'description' => $this->editForm['description'],
            'start_date' => $this->editForm['start_date'],
            'end_date' => $this->editForm['end_date'],
            'priority' => $this->editForm['priority'],
            'status' => $this->editForm['status'],
            'completed_percentage' => $this->editForm['completed_percentage']
        ]);

        if ($result['success']) {
            session()->flash('message', '일정이 성공적으로 수정되었습니다.');
            $this->editMode = false;
            $this->closeEventDetailModal();
            $this->loadCalendarData();
        } else {
            session()->flash('error', $result['message'] ?? '수정에 실패했습니다.');
        }
    }

    public function loadCalendarData()
    {
        $service = new Service();

        // 날짜 범위 계산
        $currentDate = Carbon::parse($this->currentDate);
        if ($this->viewMode === 'month') {
            $startDate = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $currentDate->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $currentDate->copy()->startOfWeek()->format('Y-m-d');
            $endDate = $currentDate->copy()->endOfWeek()->format('Y-m-d');
        }

        $result = $service->execute([
            'priority' => $this->filterPriority,
            'status' => $this->filterStatus,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->requests = $result['data'];
        $this->calendarEvents = $this->groupEventsByDate($result['data']);
    }

    private function groupEventsByDate(array $events): array
    {
        $grouped = [];
        foreach ($events as $event) {
            // start_date와 end_date가 모두 있으면 기간 내 모든 날짜에 이벤트 추가
            $startDate = $event['start_date'] ?? $event['date'];
            $endDate = $event['end_date'] ?? $event['date'];

            if ($startDate && $endDate) {
                $current = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);

                // 시작일부터 종료일까지 모든 날짜에 이벤트 추가
                while ($current <= $end) {
                    $dateKey = $current->format('Y-m-d');
                    if (!isset($grouped[$dateKey])) {
                        $grouped[$dateKey] = [];
                    }

                    // 이벤트에 현재 날짜 정보 추가
                    $eventWithDate = array_merge($event, [
                        'current_date' => $dateKey,
                        'is_start' => $current->format('Y-m-d') === $startDate,
                        'is_end' => $current->format('Y-m-d') === $endDate,
                        'is_multi_day' => $startDate !== $endDate,
                    ]);

                    $grouped[$dateKey][] = $eventWithDate;
                    $current->addDay();
                }
            } else {
                // 날짜 정보가 없으면 기본 날짜에만 추가
                $date = $event['date'];
                if (!isset($grouped[$date])) {
                    $grouped[$date] = [];
                }
                $grouped[$date][] = $event;
            }
        }
        return $grouped;
    }

    public function render()
    {
        return view('700-page-pms-calendar-view/000-index')
            ->layout('300-layout-common.000-app');
    }
}