<?php

namespace App\Livewire\Church\Events;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Gestão de Agendas')]
#[Layout('components.layouts.app')]
class Schedules extends Component
{
    public function render()
    {
        return view('church.events.schedules');
    }
}
