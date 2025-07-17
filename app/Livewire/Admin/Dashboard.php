<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Title("Dashboard Admin")]
    #[Layout('components.layouts.layout-admin-dashboard')]
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
