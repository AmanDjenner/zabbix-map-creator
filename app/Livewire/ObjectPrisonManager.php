<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ObjectPrison;
use App\Models\Institution;
use App\Models\ObjectList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ObjectPrisonManager extends Component
{
    public $objects;
    public $institutions;
    public $objectLists;
    public $showModal = false;
    public $showObjectListModal = false;
    public $editingObjectId = null;
    public $selectedDate;
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
            if (!Schema::hasTable('object_prisons')) {
                throw new \Exception('Tabela object_prisons nu există în baza de date.');
            }
            $this->institutions = Institution::all();
            $this->objectLists = ObjectList::all();
            $this->objects = new Collection();
            $this->data = Carbon::today()->format('Y-m-d'); 
            $this->selectedDate = Carbon::today()->format('Y-m-d'); 
            $this->loadObjects();
        } catch (\Exception $e) {
            Log::error('Eroare la inițializarea componentei: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la inițializarea componentei: ' . $e->getMessage()]);
        }
    }

    public function updatedSelectedDate($value)
    {
        $this->loadObjects();
    }

    public function loadObjects()
    {
        try {
            $query = ObjectPrison::with(['institution', 'objectListItems', 'createdBy', 'updatedBy']);
            if ($this->selectedDate) {
                $query->whereDate('data', $this->selectedDate);
            }
            $this->objects = $query->get();
            if ($this->objects->count() > 1000) {
                $this->objects = $this->objects->take(1000);
                Log::warning('Colecția de obiecte a fost limitată la 1000 de intrări.');
            }
        } catch (\Exception $e) {
            $this->objects = new Collection();
            Log::error('Eroare la încărcarea obiectelor: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la încărcarea obiectelor: ' . $e->getMessage()]);
        }
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
            $this->loadObjects();
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

    public function editObject($id)
    {
        try {
            $object = ObjectPrison::with('objectListItems')->find($id);
            if (!$object) {
                throw new \Exception("Obiectul cu ID-ul {$id} nu a fost găsit.");
            }

            $this->editingObjectId = $id;
            $this->data = $object->data;
            $this->id_institution = $object->id_institution;
            $this->eveniment_type = $object->eveniment ?? 'Depistare';
            $this->obj_text = $object->obj_text;

            $this->selectedObjects = $object->objectListItems->isNotEmpty()
                ? $object->objectListItems->map(function ($item) {
                    return [
                        'object_list_id' => $item->id,
                        'name' => $item->name,
                        'quantity' => $item->pivot->quantity ?? 0,
                    ];
                })->toArray()
                : [];

            $this->tempQuantities = collect($this->selectedObjects)->pluck('quantity', 'object_list_id')->all();
            $this->showModal = true;
        } catch (\Exception $e) {
            Log::error('Eroare la editarea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la editarea obiectului: ' . $e->getMessage()]);
        }
    }

    public function updateObject()
    {
        $this->validate();

        try {
            $object = ObjectPrison::findOrFail($this->editingObjectId);
            $attributes = [
                'data' => $this->data,
                'id_institution' => $this->id_institution,
                'eveniment' => $this->eveniment_type,
                'obj_text' => $this->obj_text,
                'updated_by' => Auth::id(),
            ];

            if ($this->eveniment_type && $this->eveniment_type !== 'Depistare') {
                $attributes['eveniment'] = $this->eveniment_type;
            } else {
                $attributes['eveniment'] = null;
            }

            $object->update($attributes);

            $syncData = collect($this->selectedObjects)
                ->filter(function ($item) {
                    return $item['quantity'] > 0 && isset($item['object_list_id']);
                })
                ->mapWithKeys(function ($item) {
                    return [$item['object_list_id'] => ['quantity' => $item['quantity']]];
                })->all();
            $object->objectListItems()->sync($syncData);

            $this->resetForm();
            $this->loadObjects();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Obiect actualizat cu succes!']);
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            Log::error('Eroare la actualizarea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la actualizarea obiectului: ' . $e->getMessage()]);
        }
    }

    public function deleteObject($id)
    {
        try {
            ObjectPrison::findOrFail($id)->delete();
            $this->loadObjects();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Obiect șters cu succes!']);
        } catch (\Exception $e) {
            Log::error('Eroare la ștergerea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la ștergerea obiectului: ' . $e->getMessage()]);
        }
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

    public function resetForm()
    {
        $this->showModal = false;
        $this->showObjectListModal = false;
        $this->editingObjectId = null;
        $this->data = Carbon::today()->format('Y-m-d');
        $this->id_institution = null;
        $this->eveniment_type = 'Depistare';
        $this->obj_text = null;
        $this->selectedObjects = [];
        $this->tempQuantities = [];
        $this->resetErrorBag();
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
        $this->loadObjects();
        return view('livewire.object-prison-manager', [
            'objects' => $this->objects,
        ]);
    }
}