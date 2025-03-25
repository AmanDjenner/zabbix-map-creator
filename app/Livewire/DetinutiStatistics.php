<?php

namespace App\Livewire;

use App\Models\Detinuti;
use App\Models\Institution;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DetinutiStatistics extends Component
{
    public $statisticsDate; 
    public $statistics = [];
    public $detinuti = [];
    public $institutions = [];
    public $showAddModal = false;
    public $canAddDetinuti = true;
    public $timeUntilNextAdd = '';
    public $data;
    public $total;
    public $real_inmates;
    public $in_search;
    public $pretrial_detention;
    public $initial_conditions;
    public $life;
    public $female;
    public $minors;
    public $open_sector;
    public $no_escort;
    public $monitoring_bracelets;
    public $hunger_strike;
    public $disciplinary_insulator;
    public $admitted_to_hospitals;
    public $employed_ip_in_hospitals;
    public $employed_dds_in_hospitals;
    public $work_outside;
    public $employed_ip_work_outside;
    public $editingDetinutId = null;
    public $activeTab = 'raw-data'; 

    protected $indicators = [
        'total' => 'Total',
        'real_inmates' => 'Deținuți reali',
        'in_search' => 'În căutare',
        'pretrial_detention' => 'Detenție preventivă',
        'initial_conditions' => 'Condiții inițiale',
        'life' => 'Pe viață',
        'female' => 'Femei',
        'minors' => 'Minori',
        'open_sector' => 'Sector deschis',
        'no_escort' => 'Fără escortă',
        'monitoring_bracelets' => 'Brățări monitorizare',
        'hunger_strike' => 'Grevă foame',
        'disciplinary_insulator' => 'Izolator disciplinar',
        'admitted_to_hospitals' => 'Internați spitale',
        'employed_ip_in_hospitals' => 'Angajați IP spitale',
        'employed_dds_in_hospitals' => 'Angajați DDS spitale',
        'work_outside' => 'Muncă exterior',
        'employed_ip_work_outside' => 'Angajați IP exterior',
    ];

    protected $rules = [
        'data' => 'required|date',
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
    ];

    public function mount()
    {
        $this->statisticsDate = Carbon::today()->format('Y-m-d');
        $this->institutions = Institution::whereIn('id', array_merge(range(1, 13), range(15, 18)))
            ->orderBy('id')
            ->get();
        $this->activeTab = 'raw-data';
        $this->loadData();
        $this->checkCanAddDetinuti();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadData(); 
    }

    private function checkCanAddDetinuti()
    {
        $user = Auth::user();
        if (!$user || !$user->id_institution) {
            $this->canAddDetinuti = false;
            $this->timeUntilNextAdd = 'N/A';
            return;
        }

        $today = Carbon::today()->startOfDay();
        $existingEntry = Detinuti::where('id_institution', $user->id_institution)
            ->whereDate('data', $today)
            ->exists();

        if ($existingEntry) {
            $this->canAddDetinuti = false;
            $this->updateTimeUntilNextAdd();
        } else {
            $this->canAddDetinuti = true;
            $this->timeUntilNextAdd = '';
        }
    }

    private function updateTimeUntilNextAdd()
    {
        $now = Carbon::now();
        $nextDay = Carbon::tomorrow()->startOfDay();
        $diff = $nextDay->diff($now);

        $this->timeUntilNextAdd = sprintf(
            '%02d:%02d:%02d',
            $diff->h,
            $diff->i,
            $diff->s
        );
    }

    public function openAddModal()
    {
        $this->data = Carbon::today()->format('Y-m-d');
        $this->resetFormFields();
        $this->editingDetinutId = null;
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->resetFormFields();
        $this->editingDetinutId = null;
        $this->resetErrorBag();
    }

    public function storeDetinuti()
    {
        $user = Auth::user();
        if (!$user || !$user->id_institution) {
            session()->flash('error', 'Nu aveți o instituție asociată.');
            return;
        }

        $this->validate();

        $today = Carbon::today()->startOfDay();
        $existingEntry = Detinuti::where('id_institution', $user->id_institution)
            ->whereDate('data', $today)
            ->exists();

        if ($existingEntry && !$this->editingDetinutId) {
            session()->flash('error', 'Datele pentru astăzi au fost deja adăugate pentru această instituție.');
            $this->closeAddModal();
            return;
        }

        $attributes = [
            'data' => $this->data,
            'id_institution' => $user->id_institution,
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
        ];

        if ($this->editingDetinutId) {
            $detinut = Detinuti::findOrFail($this->editingDetinutId);
            if ($detinut->id_institution !== $user->id_institution) {
                session()->flash('error', 'Nu aveți permisiunea să editați această înregistrare.');
                $this->closeAddModal();
                return;
            }
            $detinut->update($attributes);
            session()->flash('message', 'Datele au fost actualizate cu succes.');
        } else {
            Detinuti::create($attributes);
            session()->flash('message', 'Datele au fost adăugate cu succes.');
        }

        $this->closeAddModal();
        $this->checkCanAddDetinuti();
        $this->loadData();
    }

    public function editDetinut($id)
    {
        $user = Auth::user();
        $detinut = Detinuti::findOrFail($id);

        if ($detinut->id_institution !== $user->id_institution || $detinut->data->toDateString() !== Carbon::today()->toDateString()) {
            session()->flash('error', 'Puteți edita doar înregistrările pentru data curentă și instituția dumneavoastră.');
            return;
        }

        $this->editingDetinutId = $id;
        $this->data = $detinut->data->format('Y-m-d');
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
        $this->showAddModal = true;
    }

    public function deleteDetinut($id)
    {
        $user = Auth::user();
        $detinut = Detinuti::findOrFail($id);

        if ($detinut->id_institution !== $user->id_institution || $detinut->data->toDateString() !== Carbon::today()->toDateString()) {
            session()->flash('error', 'Puteți șterge doar înregistrările pentru data curentă și instituția dumneavoastră.');
            return;
        }

        $detinut->delete();
        session()->flash('message', 'Înregistrarea a fost ștearsă cu succes.');
        $this->loadData();
        $this->checkCanAddDetinuti();
    }

    private function resetFormFields()
    {
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
    }

    private function fetchStatisticsForDate($date)
    {
        try {
            if (!$date) {
                return [];
            }

            $data = Detinuti::whereDate('data', $date)
                ->whereIn('id_institution', array_merge(range(1, 13), range(15, 18)))
                ->select('id_institution')
                ->selectRaw('SUM(total) as total')
                ->selectRaw('SUM(real_inmates) as real_inmates')
                ->selectRaw('SUM(in_search) as in_search')
                ->selectRaw('SUM(pretrial_detention) as pretrial_detention')
                ->selectRaw('SUM(initial_conditions) as initial_conditions')
                ->selectRaw('SUM(life) as life')
                ->selectRaw('SUM(female) as female')
                ->selectRaw('SUM(minors) as minors')
                ->selectRaw('SUM(open_sector) as open_sector')
                ->selectRaw('SUM(no_escort) as no_escort')
                ->selectRaw('SUM(monitoring_bracelets) as monitoring_bracelets')
                ->selectRaw('SUM(hunger_strike) as hunger_strike')
                ->selectRaw('SUM(disciplinary_insulator) as disciplinary_insulator')
                ->selectRaw('SUM(admitted_to_hospitals) as admitted_to_hospitals')
                ->selectRaw('SUM(employed_ip_in_hospitals) as employed_ip_in_hospitals')
                ->selectRaw('SUM(employed_dds_in_hospitals) as employed_dds_in_hospitals')
                ->selectRaw('SUM(work_outside) as work_outside')
                ->selectRaw('SUM(employed_ip_work_outside) as employed_ip_work_outside')
                ->groupBy('id_institution')
                ->get()
                ->keyBy('id_institution');

            return $data->toArray();
        } catch (\Exception $e) {
            Log::error('Eroare la preluarea statisticilor pentru data ' . $date . ': ' . $e->getMessage());
            return [];
        }
    }

    private function fetchDetinutiForDate($date)
    {
        try {
            if (!$date) {
                return [];
            }

            $user = Auth::user();
            if (!$user || !$user->id_institution) {
                return [];
            }

            return Detinuti::with('institution')
                ->whereDate('data', $date)
                ->where('id_institution', $user->id_institution)
                ->get();
        } catch (\Exception $e) {
            Log::error('Eroare la preluarea datelor deținuților pentru data ' . $date . ': ' . $e->getMessage());
            return [];
        }
    }

    public function loadData()
    {
        try {
            // Always load raw data for today
            $this->detinuti = $this->fetchDetinutiForDate(Carbon::today()->format('Y-m-d'));

            // Load statistics only if in statistics tab
            if ($this->activeTab === 'statistics' && $this->statisticsDate) {
                $this->statistics = $this->fetchStatisticsForDate($this->statisticsDate);
            } else {
                $this->statistics = []; // Clear statistics if not in statistics tab or no date selected
            }

            $formattedStatsDate = $this->statisticsDate ? Carbon::parse($this->statisticsDate)->format('d-m-Y') : 'astăzi';
            if ($this->detinuti->isEmpty()) {
                session()->flash('message', 'Nu există date detaliate pentru data curentă.');
            } elseif ($this->activeTab === 'statistics' && empty($this->statistics)) {
                session()->flash('message', "Nu există date statistice pentru data selectată: $formattedStatsDate.");
            } elseif ($this->activeTab === 'statistics' && !empty($this->statistics)) {
                session()->flash('message', "Date statistice actualizate pentru: $formattedStatsDate.");
            }
        } catch (\Exception $e) {
            Log::error('Eroare la încărcarea datelor: ' . $e->getMessage());
            session()->flash('error', 'A apărut o eroare la încărcarea datelor.');
            $this->statistics = [];
            $this->detinuti = [];
        }
    }

    public function updatedStatisticsDate($value)
    {
        $this->statisticsDate = $value;
        $this->loadData();
    }

    public function render()
    {
        if (!$this->canAddDetinuti) {
            $this->updateTimeUntilNextAdd();
        }

        return view('livewire.detinuti-statistics', [
            'indicators' => $this->indicators,
        ]);
    }
}