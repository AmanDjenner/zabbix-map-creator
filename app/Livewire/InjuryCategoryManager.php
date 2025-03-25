<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\InjuryCategory;

class InjuryCategoryManager extends Component
{
    public $categories = [];
    public $name = '';
    public $editingCategoryId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:injuries_category,name',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->categories = InjuryCategory::all();
    }

    public function createCategory()
    {
        $this->validate();

        InjuryCategory::create(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function editCategory($id)
    {
        $category = InjuryCategory::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->name = $category->name;
        $this->showModal = true;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:injuries_category,name,' . $this->editingCategoryId,
        ]);

        $category = InjuryCategory::findOrFail($this->editingCategoryId);
        $category->update(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function deleteCategory($id)
    {
        $category = InjuryCategory::findOrFail($id);
        $category->delete();

        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->editingCategoryId = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.injury-category-manager');
    }
}