<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManager extends Component
{
    public $roles;
    public $name = '';
    public $selectedPermissions = [];
    public $editingRoleId = null;
    public $showModal = false;
    public $groupedPermissions = [];
    public $groupNames = [];
    public $editingGroup = null;

    protected $rules = [
        'name' => 'required|string|max:100|unique:roles,name',
        'selectedPermissions' => 'array',
        'groupNames.*' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->loadData();
        $this->loadGroupedPermissions();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->get();
    }

    public function loadGroupedPermissions()
    {
        // Folosim coloana 'group' din baza de date, cu fallback la gruparea automată
        $permissions = Permission::all()->groupBy(function ($permission) {
            return $permission->group ?? explode(' ', $permission->name)[1] ?? $permission->name;
        })->map(function ($group) {
            return $group->pluck('name', 'id')->toArray();
        })->toArray();

        $this->groupedPermissions = $permissions;

        // Inițializăm denumirile grupurilor cu valorile din baza de date sau implicite
        foreach (array_keys($permissions) as $group) {
            $this->groupNames[$group] = ucfirst($group);
        }
    }

    public function editGroup($group)
    {
        $this->editingGroup = $group;
    }

    public function saveGroup($group)
    {
        $this->validateOnly("groupNames.{$group}");

        // Actualizăm permanent coloana 'group' în baza de date
        if (isset($this->groupNames[$group]) && $this->groupNames[$group] !== ucfirst($group)) {
            Permission::whereIn('name', array_values($this->groupedPermissions[$group]))
                ->update(['group' => $this->groupNames[$group]]);
        }

        $this->editingGroup = null;
        $this->loadGroupedPermissions(); // Reîncărcăm pentru a reflecta schimbările
    }

    public function createRole()
    {
        $this->validate();
        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);
        $this->resetForm();
        $this->loadData();
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->editingRoleId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showModal = true;
        $this->loadGroupedPermissions();
    }

    public function updateRole()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $this->editingRoleId,
        ]);
        $role = Role::findOrFail($this->editingRoleId);
        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);
        $this->resetForm();
        $this->loadData();
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->selectedPermissions = [];
        $this->editingRoleId = null;
        $this->showModal = false;
        $this->groupNames = [];
        $this->editingGroup = null;
    }

    public function render()
    {
        return view('livewire.role-manager');
    }
}