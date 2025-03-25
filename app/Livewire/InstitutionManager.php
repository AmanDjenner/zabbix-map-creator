<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Institution;

class InstitutionManager extends Component
{
    public $institutions;
    public $name = '';
    public $editingInstitutionId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:500|unique:institutions,name',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->institutions = Institution::all();
    }

    public function createInstitution()
    {
        $this->validate();

        Institution::create(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function editInstitution($id)
    {
        $institution = Institution::findOrFail($id);
        $this->editingInstitutionId = $id;
        $this->name = $institution->name;
        $this->showModal = true;
    }

    public function updateInstitution()
    {
        $this->validate([
            'name' => 'required|string|max:500|unique:institutions,name,' . $this->editingInstitutionId,
        ]);

        $institution = Institution::findOrFail($this->editingInstitutionId);
        $institution->update(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function deleteInstitution($id)
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();

        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->editingInstitutionId = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.institution-manager');
    }
}