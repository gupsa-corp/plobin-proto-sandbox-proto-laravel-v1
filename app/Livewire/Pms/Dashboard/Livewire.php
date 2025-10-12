<?php

namespace App\Livewire\Pms\Dashboard;

use Livewire\Component;
use App\Services\Pms\Dashboard\Service;

class Livewire extends Component
{
    public $stats;
    public $recentProjects;
    public $tasks;
    public $notifications;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $service = new Service();
        $data = $service->execute();
        
        $this->stats = $data['stats'];
        $this->recentProjects = $data['recentProjects'];
        $this->tasks = $data['tasks'];
        $this->notifications = $data['notifications'];
    }

    public function render()
    {
        return view('700-page-pms-dashboard.000-index')
            ->layout('700-page-pms-common.000-layout', ['title' => '대시보드']);
    }
}