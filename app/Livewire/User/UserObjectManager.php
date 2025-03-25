<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\ObjectPrison;
use App\Models\Institution;
use App\Models\ObjectList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserObjectManager extends Component
{
    public $userObjects;
    public $allObjects;
    public $institutions;
    public $objectLists;
    public $selectedDate;
    public $activeTab = 'user-objects';
    public $showModal = false;
    public $showObjectListModal = false;
    public $data;
    public $id_institution;
    public $eveniment_type = 'Depistare';
    public $obj_text;
    public $selectedObjects = [];
    public $tempQuantities = [];

    protected $rules = [
        'data' => 'required|date|before_or_equal:today',
        'id_institution' => 'required|exists:institutions,id',
        'eveniment_type' => 'nullable|in:Depistare,Contracarare',
        'obj_text' => 'nullable|string',
        'selectedObjects.*.object_list_id' => 'nullable|exists:object_list,id',
        'selectedObjects.*.quantity' => 'nullable|integer|min:0',
    ];

    public function mount()
    {
        try {
            $this->institutions = Institution::all();
            $this->objectLists = ObjectList::all();
            $this->userObjects = collect();
            $this->allObjects = collect();
            $this->selectedDate = Carbon::today()->format('Y-m-d');
            $this->data = Carbon::today()->format('Y-m-d');
            $this->id_institution = Auth::user()->institution ? Auth::user()->institution->id : null;
            $this->loadUserObjects();
            $this->loadAllObjects();
        } catch (\Exception $e) {
            Log::error('Eroare la inițializarea componentei: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la inițializarea componentei: ' . $e->getMessage()]);
        }
    }

    public function updatedSelectedDate($value)
    {
        $this->selectedDate = $value;
        $this->loadUserObjects();
        $this->loadAllObjects();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadUserObjects()
    {
        try {
            $userInstitutionId = Auth::user()->institution ? Auth::user()->institution->id : null;
            if (!$userInstitutionId) {
                throw new \Exception('Utilizatorul nu are o instituție asociată.');
            }

            $query = ObjectPrison::with(['institution', 'objectListItems'])
                ->where('id_institution', $userInstitutionId);
            if ($this->selectedDate) {
                $query->whereDate('data', $this->selectedDate);
            }
            $this->userObjects = $query->get();
        } catch (\Exception $e) {
            $this->userObjects = collect();
            Log::error('Eroare la încărcarea obiectelor utilizatorului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la încărcarea obiectelor: ' . $e->getMessage()]);
        }
    }

    public function loadAllObjects()
    {
        try {
            $query = ObjectPrison::with(['institution', 'objectListItems']);
            if ($this->selectedDate) {
                $query->whereDate('data', $this->selectedDate);
            }
            $this->allObjects = $query->get();
        } catch (\Exception $e) {
            $this->allObjects = collect();
            Log::error('Eroare la încărcarea tuturor obiectelor: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la încărcarea tuturor obiectelor: ' . $e->getMessage()]);
        }
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function createObject()
    {
        $this->validate();

        try {
            $attributes = [
                'data' => $this->data,
                'id_institution' => $this->id_institution,
                'obj_text' => $this->obj_text,
                'eveniment' => $this->eveniment_type,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            if ($this->eveniment_type && $this->eveniment_type !== 'Depistare') {
                $attributes['eveniment'] = $this->eveniment_type;
            }

            $object = ObjectPrison::create($attributes);

            if (!empty($this->selectedObjects)) {
                foreach ($this->selectedObjects as $selectedObject) {
                    if ($selectedObject['quantity'] > 0 && isset($selectedObject['object_list_id'])) {
                        $object->objectListItems()->attach($selectedObject['object_list_id'], [
                            'quantity' => $selectedObject['quantity'],
                        ]);
                    }
                }
            }

            $createdAt = Carbon::parse($object->created_at)->format('d-m-Y H:i');
            $this->resetForm();
            $this->loadUserObjects();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => "Obiect creat cu succes la data: {$createdAt}"
            ]);
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            Log::error('Eroare la crearea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la crearea obiectului: ' . $e->getMessage()]);
        }
    }

    public function resetForm()
    {
        $this->showModal = false;
        $this->showObjectListModal = false;
        $this->data = Carbon::today()->format('Y-m-d');
        $this->id_institution = Auth::user()->institution ? Auth::user()->institution->id : null;
        $this->eveniment_type = 'Depistare';
        $this->obj_text = null;
        $this->selectedObjects = [];
        $this->tempQuantities = [];
        $this->resetErrorBag();
    }

    public function addAllSelectedObjects()
    {
        try {
            foreach ($this->tempQuantities as $objectListId => $quantity) {
                if ($quantity !== null && $quantity >= 0) {
                    $object = ObjectList::find($objectListId);
                    if ($object) {
                        $existingIndex = collect($this->selectedObjects)->search(function ($item) use ($objectListId) {
                            return $item['object_list_id'] == $objectListId;
                        });

                        if ($existingIndex !== false) {
                            $this->selectedObjects[$existingIndex]['quantity'] = (int)$quantity;
                        } else {
                            $this->selectedObjects[] = [
                                'object_list_id' => $object->id,
                                'name' => $object->name,
                                'quantity' => (int)$quantity,
                            ];
                        }
                    }
                }
            }
            $this->tempQuantities = [];
            $this->showObjectListModal = false;
        } catch (\Exception $e) {
            Log::error('Eroare la adăugarea obiectelor selectate: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la adăugarea obiectelor: ' . $e->getMessage()]);
        }
    }

    public function removeSelectedObject($index)
    {
        try {
            if (isset($this->selectedObjects[$index])) {
                unset($this->selectedObjects[$index]);
                $this->selectedObjects = array_values($this->selectedObjects);
            }
        } catch (\Exception $e) {
            Log::error('Eroare la eliminarea obiectului selectat: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la eliminarea obiectului: ' . $e->getMessage()]);
        }
    }

    public function openObjectListModal()
    {
        try {
            $this->tempQuantities = collect($this->selectedObjects)->pluck('quantity', 'object_list_id')->all();
            $this->showObjectListModal = true;
        } catch (\Exception $e) {
            Log::error('Eroare la deschiderea modalului de selecție: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la deschiderea selecției obiectelor: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.user.user-object-manager', [
            'userObjects' => $this->userObjects,
            'allObjects' => $this->allObjects,
        ]);
    }
}