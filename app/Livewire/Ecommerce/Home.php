<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Title('Home - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class Home extends Component
{
    public function render()
    {
        return view('ecommerce.home');
    }
}