<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EventCategory;
use App\Models\EventSubcategory;

class EventCategoryManager extends Component
{
    public $categories = [];
    public $name = '';
    public $categoryId = '';
    public $selectedCategory = '';
    public $editingCategoryId = null;
    public $editingSubcategoryId = null;
    public $showCategoryModal = false;
    public $showSubcategoryModal = false;

    protected $rules = [
        'name' => 'required|string|max:255|unique:events_category,name',
        'categoryId' => 'required|exists:events_category,id',
        'selectedCategory' => 'nullable|string',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->categories = EventCategory::with('subcategories')->get();
    }

    public function createCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:events_category,name',
        ]);

        EventCategory::create(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function editCategory($id)
    {
        $category = EventCategory::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->name = $category->name;
        $this->showCategoryModal = true;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:events_category,name,' . $this->editingCategoryId,
        ]);

        $category = EventCategory::findOrFail($this->editingCategoryId);
        $category->update(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function deleteCategory($id)
    {
        $category = EventCategory::findOrFail($id);
        $category->delete();

        $this->loadData();
    }

    public function createSubcategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:events_subcategory,name',
            'categoryId' => 'required|exists:events_category,id',
        ]);

        EventSubcategory::create([
            'name' => $this->name,
            'id_events_category' => $this->categoryId,
        ]);

        $this->resetForm();
        $this->loadData();
    }

    public function editSubcategory($id)
    {
        $subcategory = EventSubcategory::findOrFail($id);
        $this->editingSubcategoryId = $id;
        $this->name = $subcategory->name;
        $this->categoryId = $subcategory->id_category;
        $this->showSubcategoryModal = true;
    }

    public function updateSubcategory()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:events_subcategory,name,' . $this->editingSubcategoryId,
            'categoryId' => 'required|exists:events_category,id',
        ]);

        $subcategory = EventSubcategory::findOrFail($this->editingSubcategoryId);
        $subcategory->update([
            'name' => $this->name,
            'id_events_category' => $this->categoryId,
        ]);

        $this->resetForm();
        $this->loadData();
    }

    public function deleteSubcategory($id)
    {
        $subcategory = EventSubcategory::findOrFail($id);
        $subcategory->delete();

        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->categoryId = '';
        $this->selectedCategory = '';
        $this->editingCategoryId = null;
        $this->editingSubcategoryId = null;
        $this->showCategoryModal = false;
        $this->showSubcategoryModal = false;
    }

    public function render()
    {
        // Nu reîncărcăm datele aici, deoarece este deja în mount() și alte metode
        return view('livewire.event-category-manager');
    }
}