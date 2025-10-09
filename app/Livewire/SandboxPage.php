<?php

namespace App\Livewire;

use Livewire\Component;

class SandboxPage extends Component
{
    public $container = 'gupsa';
    public $domain = '100-domain-pms';
    public $page = '103-screen-table-view';

    public function mount($container = 'gupsa', $domain = '100-domain-pms', $page = '101-screen-dashboard')
    {
        $this->container = $container;
        $this->domain = $domain;
        $this->page = $page;
    }

    public function render()
    {
        return view('700-page-sandbox.000-index', [
            'container' => $this->container,
            'domain' => $this->domain,
            'page' => $this->page
        ])->layout('components.layouts.app');
    }
}