<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ObjectList;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class ObjectListManager extends Component
{
    public $objectLists;
    public $showModal = false;
    public $editingObjectListId = null;
    public $name;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function mount()
    {
        try {
            $this->objectLists = new Collection();
            $this->loadObjectLists();
        } catch (\Exception $e) {
            Log::error('Eroare la inițializarea componentei: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la inițializarea componentei: ' . $e->getMessage()]);
        }
    }

    public function loadObjectLists()
    {
        try {
            $this->objectLists = ObjectList::all();
        } catch (\Exception $e) {
            $this->objectLists = new Collection();
            Log::error('Eroare la încărcarea listei de obiecte: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la încărcarea listei de obiecte: ' . $e->getMessage()]);
        }
    }

    public function createObjectList()
    {
        $this->validate();

        try {
            $objectList = ObjectList::create([
                'name' => $this->name,
            ]);

            $createdAt = Carbon::parse($objectList->created_at)->format('d-m-Y H:i');
            $this->resetForm();
            $this->loadObjectLists();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => "Obiect creat cu succes la data: {$createdAt}"
            ]);
            $this->dispatch('closeModal');

            session()->flash('message', "Obiect creat cu succes la data: {$createdAt}");
        } catch (\Exception $e) {
            Log::error('Eroare la crearea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la crearea obiectului: ' . $e->getMessage()]);
            session()->flash('error', 'Eroare la crearea obiectului: ' . $e->getMessage());
        }
    }

    public function editObjectList($id)
    {
        try {
            $objectList = ObjectList::find($id);
            if (!$objectList) {
                throw new \Exception("Obiectul cu ID-ul {$id} nu a fost găsit.");
            }

            $this->editingObjectListId = $id;
            $this->name = $objectList->name;
            $this->showModal = true;
        } catch (\Exception $e) {
            Log::error('Eroare la editarea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la editarea obiectului: ' . $e->getMessage()]);
        }
    }

    public function updateObjectList()
    {
        $this->validate();

        try {
            $objectList = ObjectList::findOrFail($this->editingObjectListId);
            $objectList->update([
                'name' => $this->name,
            ]);

            $this->resetForm();
            $this->loadObjectLists();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Obiect actualizat cu succes!']);
            $this->dispatch('closeModal');

            session()->flash('message', 'Obiect actualizat cu succes!');
        } catch (\Exception $e) {
            Log::error('Eroare la actualizarea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la actualizarea obiectului: ' . $e->getMessage()]);
            session()->flash('error', 'Eroare la actualizarea obiectului: ' . $e->getMessage());
        }
    }

    public function deleteObjectList($id)
    {
        try {
            ObjectList::findOrFail($id)->delete();
            $this->loadObjectLists();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Obiect șters cu succes!']);
            session()->flash('message', 'Obiect șters cu succes!');
        } catch (\Exception $e) {
            Log::error('Eroare la ștergerea obiectului: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'danger', 'message' => 'Eroare la ștergerea obiectului: ' . $e->getMessage()]);
            session()->flash('error', 'Eroare la ștergerea obiectului: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->showModal = false;
        $this->editingObjectListId = null;
        $this->name = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->loadObjectLists();
        return view('livewire.object-list-manager', [
            'objectLists' => $this->objectLists,
        ]);
    }
}