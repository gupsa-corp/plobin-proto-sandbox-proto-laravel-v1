<?php

namespace App\Livewire\Pms\CalendarView;

use Livewire\Component;
use App\Services\Pms\Projects\Service;

class Livewire extends Component
{
    public $projects;
    public $currentDate;
    public $viewMode = 'month';
    public $selectedDate = null;
    public $showCreateModal = false;
    public $eventForm = [
        'title' => '',
        'date' => '',
        'time' => '09:00',
        'duration' => 1,
        'type' => 'meeting',
        'priority' => 'medium'
    ];
    public $filterPriority = '';
    public $filterStatus = '';
    public $showFilters = false;

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
        $this->eventForm['date'] = $date ?: $this->selectedDate ?: $this->currentDate;
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
            'date' => '',
            'time' => '09:00',
            'duration' => 1,
            'type' => 'meeting',
            'priority' => 'medium'
        ];
    }

    public function createEvent()
    {
        $this->validate([
            'eventForm.title' => 'required|min:3',
            'eventForm.date' => 'required|date',
            'eventForm.time' => 'required',
            'eventForm.duration' => 'required|numeric|min:0.5|max:8'
        ], [
            'eventForm.title.required' => '제목을 입력해주세요.',
            'eventForm.title.min' => '제목은 3자 이상이어야 합니다.',
            'eventForm.date.required' => '날짜를 선택해주세요.',
            'eventForm.time.required' => '시간을 선택해주세요.',
            'eventForm.duration.required' => '소요시간을 입력해주세요.',
            'eventForm.duration.min' => '최소 30분 이상이어야 합니다.',
            'eventForm.duration.max' => '최대 8시간까지 가능합니다.'
        ]);

        // 실제로는 서비스를 통해 이벤트 생성
        session()->flash('message', '일정이 성공적으로 추가되었습니다.');
        $this->closeCreateModal();
        $this->loadCalendarData();
    }

    public function clearFilters()
    {
        $this->filterPriority = '';
        $this->filterStatus = '';
        $this->loadCalendarData();
    }

    public function loadCalendarData()
    {
        $service = new Service();
        $result = $service->execute([
            'priority' => $this->filterPriority,
            'status' => $this->filterStatus
        ]);
        $this->projects = $result['data'];
    }

    public function render()
    {
        return view('700-page-pms-calendar-view/000-index');
    }
}