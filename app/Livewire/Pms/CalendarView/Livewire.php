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

    public function loadCalendarData()
    {
        $service = new Service();
        $result = $service->execute();
        $this->projects = $result['data'];
    }

    public function render()
    {
        return view('700-page-pms-calendar-view/000-index');
    }
}