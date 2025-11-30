<?php

namespace App\Livewire\Church\Only;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Igreja| Boas Práticas')]
#[Layout('components.layouts.app')]
class ChuchGoodPractices extends Component
{
    public function render()
    {
        return view('church.only.chuch-good-practices');
    }
}
