<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionGroupEditor extends Component
{
    public $permissions;
    public $editingPermissionId = null;
    public $group = '';

    public function mount()
    {
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $this->permissions = Permission::all();
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $this->editingPermissionId = $id;
        $this->group = $permission->group;
    }

    public function updateGroup()
    {
        $permission = Permission::findOrFail($this->editingPermissionId);
        $permission->update(['group' => $this->group]);
        $this->resetForm();
        $this->loadPermissions();
    }

    public function resetForm()
    {
        $this->editingPermissionId = null;
        $this->group = '';
    }

    public function render()
    {
        return view('livewire.permission-group-editor');
    }
}