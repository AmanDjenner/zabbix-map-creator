// app/Http/Livewire/DetinutiCrud.php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Detinuti;
use App\Models\Institution;
use Illuminate\Support\Facades\Gate;

class DetinutiCrud extends Component
{
    public $detinuti, $institutions, $data, $id_institution, $total, $real_inmates;
    public $isOpen = false;
    public $detinutiId;

    protected $rules = [
        'data' => 'required|date',
        'id_institution' => 'required|exists:institutions,id',
        'total' => 'nullable|integer',
        'real_inmates' => 'nullable|integer',
    ];

    public function render()
    {
        $this->detinuti = Detinuti::with('institution')->get();
        $this->institutions = Institution::all();
        return view('livewire.detinuti-crud');
    }

    public function create()
    {
        if (Gate::denies('create-detinuti')) {
            abort(403);
        }
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        if (Gate::denies('create-detinuti')) {
            abort(403);
        }
        
        $this->validate();
        
        Detinuti::create([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'total' => $this->total,
            'real_inmates' => $this->real_inmates,
        
        ]);

        session()->flash('message', 'Detinuti created successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        if (Gate::denies('update-detinuti')) {
            abort(403);
        }
        
        $detinuti = Detinuti::findOrFail($id);
        $this->detinutiId = $id;
        $this->data = $detinuti->data;
        $this->id_institution = $detinuti->id_institution;
        $this->total = $detinuti->total;
        $this->real_inmates = $detinuti->real_inmates;
        $this->openModal();
    }

    public function update()
    {
        if (Gate::denies('update-detinuti')) {
            abort(403);
        }
        
        $this->validate();
        
        $detinuti = Detinuti::find($this->detinutiId);
        $detinuti->update([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'total' => $this->total,
            'real_inmates' => $this->real_inmates,
        ]);

        session()->flash('message', 'Detinuti updated successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        if (Gate::denies('delete-detinuti')) {
            abort(403);
        }
        
        Detinuti::find($id)->delete();
        session()->flash('message', 'Detinuti deleted successfully.');
    }

    private function resetInputFields()
    {
        $this->data = '';
        $this->id_institution = '';
        $this->total = '';
        $this->real_inmates = '';
        $this->detinutiId = '';
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }
}