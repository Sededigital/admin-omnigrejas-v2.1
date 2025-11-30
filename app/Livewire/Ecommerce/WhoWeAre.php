<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Quem somos nós - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class WhoWeAre extends Component
{
    public function render()
    {
        return view('ecommerce.who-we-are');
    }
}
