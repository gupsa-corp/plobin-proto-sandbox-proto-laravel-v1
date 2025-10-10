<?php

namespace Tests\Feature\Pms\CalendarView\FilterRequests\ClearFilters;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Pms\CalendarView\Livewire as CalendarLivewire;

class Test extends TestCase
{
    use RefreshDatabase;

    public function test_필터_초기화가_정상_동작한다(): void
    {
        $component = Livewire::test(CalendarLivewire::class)
            ->set('filterPriority', 'high')
            ->set('filterStatus', 'completed');

        $component->call('clearFilters');

        $component->assertSet('filterPriority', '')
            ->assertSet('filterStatus', '');
    }
}
