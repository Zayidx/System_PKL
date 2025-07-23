<?php

namespace App\Livewire\StaffHubin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Dashboard Siswa')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.staff-hubin.dashboard');
    }
}
