<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Igrejas - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class Church extends Component
{
    public function render()
    {
        return view('ecommerce.church');
    }
}
