<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Injury;
use App\Models\InjuryCategory;
use App\Models\Institution;

class InjuryManager extends Component
{
    public $injuries = [];
    public $data;
    public $id_institution;
    public $id_injuries_category;
    public $persons_involved;
    public $injuries_text;
    public $editingInjuryId = null;
    public $showModal = false;

    public $institutions = [];
    public $injuryCategories = [];

    protected $rules = [
        'data' => 'required|date',
        'id_institution' => 'required|exists:institutions,id',
        'id_injuries_category' => 'required|exists:injuries_category,id',
        'persons_involved' => 'required|integer|min:0',
        'injuries_text' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->injuries = Injury::all();
        $this->institutions = Institution::all();
        $this->injuryCategories = InjuryCategory::all();
    }

    public function createInjury()
    {
        $this->validate();
        Injury::create([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'id_injuries_category' => $this->id_injuries_category,
            'persons_involved' => $this->persons_involved,
            'injuries_text' => $this->injuries_text,
        ]);
        $this->resetForm();
        $this->loadData();
    }

    public function editInjury($id)
    {
        $injury = Injury::findOrFail($id);
        $this->editingInjuryId = $id;
        $this->data = $injury->data;
        $this->id_institution = $injury->id_institution;
        $this->id_injuries_category = $injury->id_injuries_category;
        $this->persons_involved = $injury->persons_involved;
        $this->injuries_text = $injury->injuries_text;
        $this->showModal = true;

        $this->dispatch('editInjury', [
            'data' => $this->data,
            'injuries_text' => $this->injuries_text
        ]);
    }

    public function updateInjury()
    {
        $this->validate();
        $injury = Injury::findOrFail($this->editingInjuryId);
        $injury->update([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'id_injuries_category' => $this->id_injuries_category,
            'persons_involved' => $this->persons_involved,
            'injuries_text' => $this->injuries_text,
        ]);
        $this->resetForm();
        $this->loadData();
    }

    public function deleteInjury($id)
    {
        $injury = Injury::findOrFail($id);
        $injury->delete();
        $this->loadData();
    }

    public function resetForm()
    {
        $this->data = null;
        $this->id_institution = null;
        $this->id_injuries_category = null;
        $this->persons_involved = null;
        $this->injuries_text = null;
        $this->editingInjuryId = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.injury-manager');
    }
}