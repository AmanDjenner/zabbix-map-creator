<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{
    public $permissions;
    public $name = '';
    public $editingPermissionId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:100|unique:permissions,name',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->permissions = Permission::all();
    }

    public function createPermission()
    {
        $this->validate();

        Permission::create(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $this->editingPermissionId = $id;
        $this->name = $permission->name;
        $this->showModal = true;
    }

    public function updatePermission()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:permissions,name,' . $this->editingPermissionId,
        ]);

        $permission = Permission::findOrFail($this->editingPermissionId);
        $permission->update(['name' => $this->name]);

        $this->resetForm();
        $this->loadData();
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->editingPermissionId = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.permission-manager');
    }
}