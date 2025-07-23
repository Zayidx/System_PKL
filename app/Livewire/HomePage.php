<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth; // Import Auth facade
use Livewire\Attributes\Layout;
use Livewire\Component;

class HomePage extends Component
{
    #[Layout("components.layouts.layout-homepage")]

    public $user;

    /**
     * Lifecycle hook yang berjalan saat komponen pertama kali dimuat.
     * Kita akan mengambil data pengguna yang sedang login di sini.
     */
    public function mount()
    {
        // Mengambil seluruh data pengguna yang terotentikasi
        $this->user = Auth::user();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('homepage');
    }
    public function render()
    {
        return view('livewire.home-page');
    }
}
