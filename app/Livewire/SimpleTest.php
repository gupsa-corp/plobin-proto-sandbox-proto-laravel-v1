<?php

namespace App\Livewire;

use Livewire\Component;

class SimpleTest extends Component
{
    public $message = 'Hello, Livewire!';
    public $clickCount = 0;

    public function increment()
    {
        $this->clickCount++;
        $this->message = "Clicked {$this->clickCount} times!";
    }

    public function render()
    {
        return view('simple-test')->layout('300-layout-common.000-app');
    }
}