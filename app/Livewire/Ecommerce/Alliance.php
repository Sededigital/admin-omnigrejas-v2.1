<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Alianças - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class Alliance extends Component
{
    public function render()
    {
        return view('ecommerce.alliance');
    }
}
