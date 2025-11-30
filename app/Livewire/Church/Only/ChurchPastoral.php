<?php

namespace App\Livewire\Church\Only;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Minhas Igrejas')]
#[Layout('components.layouts.app')]
class ChurchPastoral extends Component
{
    public function render()
    {
        return view('church.only.church-pastoral');
    }
}
