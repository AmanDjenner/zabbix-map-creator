<?php

namespace App\Livewire;

use App\Models\Detinuti;
use App\Models\Institution;
use Livewire\Component;
use Carbon\Carbon;

class DetinutiManager extends Component
{
    public $detinuti = [];
    public $showModal = false;
    public $editingDetinutId = null;
    public $sortDate;
    public $data, $id_institution, $total, $real_inmates, $in_search, $pretrial_detention, $initial_conditions, $life, $female, $minors, $open_sector, $no_escort, $monitoring_bracelets, $hunger_strike, $disciplinary_insulator, $admitted_to_hospitals, $employed_ip_in_hospitals, $employed_dds_in_hospitals, $work_outside, $employed_ip_work_outside;

    protected $rules = [
        'data' => 'nullable|date',
        'id_institution' => 'required|exists:institutions,id',
        'total' => 'nullable|integer|min:0',
        'real_inmates' => 'nullable|integer|min:0',
        'in_search' => 'nullable|integer|min:0',
        'pretrial_detention' => 'nullable|integer|min:0',
        'initial_conditions' => 'nullable|integer|min:0',
        'life' => 'nullable|integer|min:0',
        'female' => 'nullable|integer|min:0',
        'minors' => 'nullable|integer|min:0',
        'open_sector' => 'nullable|integer|min:0',
        'no_escort' => 'nullable|integer|min:0',
        'monitoring_bracelets' => 'nullable|integer|min:0',
        'hunger_strike' => 'nullable|integer|min:0',
        'disciplinary_insulator' => 'nullable|integer|min:0',
        'admitted_to_hospitals' => 'nullable|integer|min:0',
        'employed_ip_in_hospitals' => 'nullable|integer|min:0',
        'employed_dds_in_hospitals' => 'nullable|integer|min:0',
        'work_outside' => 'nullable|integer|min:0',
        'employed_ip_work_outside' => 'nullable|integer|min:0',
        'sortDate' => 'nullable|date',
    ];

    public function mount()
    {
        $this->sortDate = Carbon::today()->format('Y-m-d'); 
        \Log::info('Mounting with sortDate: ' . $this->sortDate);
        $this->loadDetinuti();
    }

    public function updatedSortDate()
    {
        \Log::info('SortDate updated to: ' . $this->sortDate);
        $this->loadDetinuti();
    }

    public function loadDetinuti()
    {
        try {
            $query = Detinuti::with('institution');
            if ($this->sortDate) {
                \Log::info('Applying date filter: ' . $this->sortDate);
                $query->whereDate('data', $this->sortDate);
            } else {
                \Log::info('No sortDate set, loading nothing');
                $this->detinuti = collect();
                return;
            }
            $this->detinuti = $query->get();
            \Log::info('Loaded ' . $this->detinuti->count() . ' records');
            \Log::info('Detinuti data: ' . json_encode($this->detinuti->toArray()));
            if ($this->detinuti->isEmpty()) {
                session()->flash('message', 'Nu există date pentru ' . Carbon::parse($this->sortDate)->format('d-m-Y'));
            }
        } catch (\Exception $e) {
            $this->detinuti = [];
            \Log::error('Error loading detinuti: ' . $e->getMessage());
            session()->flash('error', 'Eroare la încărcarea datelor: ' . $e->getMessage());
        }
    }

    public function createDetinut()
    {
        $this->validate();
        Detinuti::create([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'total' => $this->total,
            'real_inmates' => $this->real_inmates,
            'in_search' => $this->in_search,
            'pretrial_detention' => $this->pretrial_detention,
            'initial_conditions' => $this->initial_conditions,
            'life' => $this->life,
            'female' => $this->female,
            'minors' => $this->minors,
            'open_sector' => $this->open_sector,
            'no_escort' => $this->no_escort,
            'monitoring_bracelets' => $this->monitoring_bracelets,
            'hunger_strike' => $this->hunger_strike,
            'disciplinary_insulator' => $this->disciplinary_insulator,
            'admitted_to_hospitals' => $this->admitted_to_hospitals,
            'employed_ip_in_hospitals' => $this->employed_ip_in_hospitals,
            'employed_dds_in_hospitals' => $this->employed_dds_in_hospitals,
            'work_outside' => $this->work_outside,
            'employed_ip_work_outside' => $this->employed_ip_work_outside,
        ]);
        $this->resetForm();
    }

    public function editDetinut($id)
    {
        $detinut = Detinuti::findOrFail($id);
        $this->editingDetinutId = $id;
        $this->data = $detinut->data ? Carbon::parse($detinut->data)->format('Y-m-d') : null;
        $this->id_institution = $detinut->id_institution;
        $this->total = $detinut->total;
        $this->real_inmates = $detinut->real_inmates;
        $this->in_search = $detinut->in_search;
        $this->pretrial_detention = $detinut->pretrial_detention;
        $this->initial_conditions = $detinut->initial_conditions;
        $this->life = $detinut->life;
        $this->female = $detinut->female;
        $this->minors = $detinut->minors;
        $this->open_sector = $detinut->open_sector;
        $this->no_escort = $detinut->no_escort;
        $this->monitoring_bracelets = $detinut->monitoring_bracelets;
        $this->hunger_strike = $detinut->hunger_strike;
        $this->disciplinary_insulator = $detinut->disciplinary_insulator;
        $this->admitted_to_hospitals = $detinut->admitted_to_hospitals;
        $this->employed_ip_in_hospitals = $detinut->employed_ip_in_hospitals;
        $this->employed_dds_in_hospitals = $detinut->employed_dds_in_hospitals;
        $this->work_outside = $detinut->work_outside;
        $this->employed_ip_work_outside = $detinut->employed_ip_work_outside;
        $this->showModal = true;
    }

    public function updateDetinut()
    {
        $this->validate();
        $detinut = Detinuti::findOrFail($this->editingDetinutId);
        $detinut->update([
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'total' => $this->total,
            'real_inmates' => $this->real_inmates,
            'in_search' => $this->in_search,
            'pretrial_detention' => $this->pretrial_detention,
            'initial_conditions' => $this->initial_conditions,
            'life' => $this->life,
            'female' => $this->female,
            'minors' => $this->minors,
            'open_sector' => $this->open_sector,
            'no_escort' => $this->no_escort,
            'monitoring_bracelets' => $this->monitoring_bracelets,
            'hunger_strike' => $this->hunger_strike,
            'disciplinary_insulator' => $this->disciplinary_insulator,
            'admitted_to_hospitals' => $this->admitted_to_hospitals,
            'employed_ip_in_hospitals' => $this->employed_ip_in_hospitals,
            'employed_dds_in_hospitals' => $this->employed_dds_in_hospitals,
            'work_outside' => $this->work_outside,
            'employed_ip_work_outside' => $this->employed_ip_work_outside,
        ]);
        $this->resetForm();
    }

    public function deleteDetinut($id)
    {
        Detinuti::findOrFail($id)->delete();
        $this->loadDetinuti();
    }

    public function resetForm()
    {
        $this->showModal = false;
        $this->editingDetinutId = null;
        $this->data = null;
        $this->id_institution = null;
        $this->total = null;
        $this->real_inmates = null;
        $this->in_search = null;
        $this->pretrial_detention = null;
        $this->initial_conditions = null;
        $this->life = null;
        $this->female = null;
        $this->minors = null;
        $this->open_sector = null;
        $this->no_escort = null;
        $this->monitoring_bracelets = null;
        $this->hunger_strike = null;
        $this->disciplinary_insulator = null;
        $this->admitted_to_hospitals = null;
        $this->employed_ip_in_hospitals = null;
        $this->employed_dds_in_hospitals = null;
        $this->work_outside = null;
        $this->employed_ip_work_outside = null;
        $this->resetErrorBag();
        $this->loadDetinuti();
    }

    public function render()
    {
        return view('livewire.detinuti-manager', [
            'institutions' => Institution::all(),
        ]);
    }
}