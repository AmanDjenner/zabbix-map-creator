<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Institution;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class UserManager extends Component
{
    public $users;
    public $roles;
    public $institutions;
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRole = '';
    public $selectedInstitution = '';
    public $editingUserId = null;
    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'selectedRole' => 'required|exists:roles,name',
        'selectedInstitution' => 'nullable|exists:institutions,id',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->users = User::with(['roles', 'institution'])->get();
        $this->roles = Role::all();
        $this->institutions = Institution::all();
    }

    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'id_institution' => $this->selectedInstitution ?: null,
        ]);
        $user->assignRole($this->selectedRole);

        $this->resetForm();
        $this->loadData();
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = ''; // Parola nu se precompletează
        $this->selectedRole = $user->roles->first()->name ?? '';
        $this->selectedInstitution = $user->id_institution;
        $this->showModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->editingUserId,
            'password' => 'nullable|string|min:8',
            'selectedRole' => 'required|exists:roles,name',
            'selectedInstitution' => 'nullable|exists:institutions,id',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'id_institution' => $this->selectedInstitution ?: null,
        ];
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }
        $user->update($data);
        $user->syncRoles([$this->selectedRole]);

        $this->resetForm();
        $this->loadData();
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->loadData();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRole = '';
        $this->selectedInstitution = '';
        $this->editingUserId = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.user-manager');
    }
}