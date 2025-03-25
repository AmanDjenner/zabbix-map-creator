<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDashboard extends Component
{
    public $activeTab = 'users';

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'users');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.admin-dashboard')
            ->layout('components.layouts.app.sidebar', [
                'activeTab' => $this->activeTab,
            ]);
    }
}