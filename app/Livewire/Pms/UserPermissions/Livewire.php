<?php

namespace App\Livewire\Pms\UserPermissions;

use Livewire\Component;

class Livewire extends Component
{
    public $currentUser;
    public $permissions;
    public $roles;

    public function mount()
    {
        $this->loadUserPermissions();
    }

    public function loadUserPermissions()
    {
        $service = new \App\Services\Pms\UserPermissions\Service();
        $data = $service->execute();

        $this->currentUser = $data['currentUser'];
        $this->permissions = $data['permissions'];
        $this->roles = $data['roles'];
    }

    public function render()
    {
        return view('700-page-pms-user-permissions.000-index')
            ->layout('700-page-pms-common.000-layout', ['title' => '권한관리']);
    }
}