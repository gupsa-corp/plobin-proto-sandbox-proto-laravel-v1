<?php

namespace App\Livewire\Pms\UnifiedView;

use Livewire\Component;
use App\Services\Pms\GetCalendarRequests\Service as CalendarService;
use App\Services\Pms\Projects\Service as ProjectsService;
use App\Services\Pms\Kanban\Service as KanbanService;

class Livewire extends Component
{
    public $currentTab = 'projects';
    public $projects = [];
    public $calendarEvents = [];
    public $kanbanProjects = [];

    // 필터 및 검색
    public $search = '';
    public $status = '';
    public $priority = '';

    public function mount()
    {
        // URL 쿼리 파라미터에서 탭 읽기
        $this->currentTab = request()->query('tab', 'projects');
        $this->loadDataForCurrentTab();
    }

    public function loadDataForCurrentTab()
    {
        switch ($this->currentTab) {
            case 'projects':
            case 'table':
                $this->loadProjects();
                break;
            case 'calendar':
                $this->loadCalendarData();
                break;
            case 'kanban':
                $this->loadKanbanData();
                break;
            case 'gantt':
                $this->loadProjects(); // Gantt도 프로젝트 데이터 사용
                break;
        }
    }

    private function loadProjects()
    {
        $service = new ProjectsService();
        $result = $service->execute([
            'search' => $this->search,
            'status' => $this->status,
            'priority' => $this->priority,
            'sortBy' => 'created_at',
            'sortDirection' => 'desc'
        ]);
        $this->projects = $result['data'];
    }

    private function loadCalendarData()
    {
        $service = new CalendarService();
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->endOfMonth()->format('Y-m-d');

        $result = $service->execute([
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->projects = $result['data'];
        $this->calendarEvents = $this->groupEventsByDate($result['data']);
    }

    private function loadKanbanData()
    {
        $service = new KanbanService();
        $result = $service->execute([
            'search' => $this->search,
            'priority' => $this->priority,
        ]);
        $this->kanbanProjects = $result['data'];
    }

    private function groupEventsByDate(array $events): array
    {
        $grouped = [];
        foreach ($events as $event) {
            $startDate = $event['start_date'] ?? $event['date'];
            $endDate = $event['end_date'] ?? $event['date'];

            if ($startDate && $endDate) {
                $current = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);

                while ($current <= $end) {
                    $dateKey = $current->format('Y-m-d');
                    if (!isset($grouped[$dateKey])) {
                        $grouped[$dateKey] = [];
                    }

                    $eventWithDate = array_merge($event, [
                        'current_date' => $dateKey,
                        'is_start' => $current->format('Y-m-d') === $startDate,
                        'is_end' => $current->format('Y-m-d') === $endDate,
                        'is_multi_day' => $startDate !== $endDate,
                    ]);

                    $grouped[$dateKey][] = $eventWithDate;
                    $current->addDay();
                }
            }
        }
        return $grouped;
    }

    public function render()
    {
        return view('700-page-pms-unified-view.000-index')
            ->layout('300-layout-common.000-app');
    }
}
