<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Event;
use App\Models\Institution;
use App\Models\EventCategory;
use App\Models\EventSubcategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class EventManager24H extends Component
{
    public $events;
    public $allEvents;
    public $categories;
    public $subcategories;
    public $showModal = false;
    public $editingEventId = null;
    public $data;
    public $id_institution;
    public $id_events_category;
    public $id_subcategory = [];
    public $persons_involved;
    public $events_text;
    public $filterDate;
    public $activeTab = 'added-events';
    public $lastLoadedDate;

    public function rules()
    {
        return [
            'data' => 'required|date',
            'id_events_category' => 'required|exists:events_category,id',
            'id_subcategory' => 'nullable|array',
            'id_subcategory.*' => 'exists:events_subcategory,id',
            'persons_involved' => 'nullable|integer|min:0',
            'events_text' => 'nullable|string',
        ];
    }

    public function mount()
    {
        try {
            $this->categories = EventCategory::all();
            $this->subcategories = collect();
            $this->events = collect();
            $this->allEvents = collect();
            $this->data = Carbon::today()->format('Y-m-d');
            $this->filterDate = Carbon::today()->format('Y-m-d');
            $this->id_institution = Auth::user()->institution ? Auth::user()->institution->id : null;
            $this->lastLoadedDate = Carbon::today()->subDays(6)->format('Y-m-d');
            $this->loadInitialEvents();
            $this->loadAllEvents();
            Log::info('Mount: id_institution setat la:', ['id_institution' => $this->id_institution]);
        } catch (\Exception $e) {
            Log::error('Eroare la inițializarea componentei: ' . $e->getMessage());
            session()->flash('error', 'Eroare la inițializarea componentei: ' . $e->getMessage());
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        if ($tab === 'added-events') {
            $this->lastLoadedDate = Carbon::today()->subDays(6)->format('Y-m-d');
            $this->events = collect();
            $this->loadInitialEvents();
        } elseif ($tab === 'all-events') {
            $this->loadAllEvents();
        }
    }

    public function loadInitialEvents()
    {
        try {
            $startDate = Carbon::parse($this->lastLoadedDate);
            $endDate = Carbon::today();

            $query = Event::with(['institution', 'category', 'subcategories'])
                ->where('id_institution', $this->id_institution)
                ->whereBetween('data', [$startDate, $endDate])
                ->orderBy('data', 'desc');

            $events = $query->get();

            $groupedByDate = $events->groupBy(function ($event) {
                return Carbon::parse($event->data)->format('Y-m-d');
            });

            foreach ($groupedByDate as $date => $dayEvents) {
                $this->events->put($date, $dayEvents);
            }

            Log::info('Evenimente inițiale încărcate (ultimele 7 zile):', $this->events->toArray());
        } catch (\Exception $e) {
            Log::error('Eroare la încărcarea evenimentelor inițiale: ' . $e->getMessage());
            session()->flash('error', 'Eroare la încărcarea evenimentelor inițiale: ' . $e->getMessage());
        }
    }

    public function loadEvents()
    {
        try {
            $query = Event::with(['institution', 'category', 'subcategories'])
                ->where('id_institution', $this->id_institution)
                ->whereDate('data', $this->lastLoadedDate)
                ->orderBy('data', 'desc');

            $dayEvents = $query->get();

            if ($dayEvents->isNotEmpty()) {
                $this->events->put($this->lastLoadedDate, $dayEvents);
            }

            Log::info('Events încărcate pentru ziua:', [$this->lastLoadedDate => $dayEvents->toArray()]);
        } catch (\Exception $e) {
            Log::error('Eroare la încărcarea evenimentelor: ' . $e->getMessage());
            session()->flash('error', 'Eroare la încărcarea evenimentelor: ' . $e->getMessage());
        }
    }

    public function loadMoreEvents()
    {
        $this->lastLoadedDate = Carbon::parse($this->lastLoadedDate)->subDay()->format('Y-m-d');
        $this->loadEvents();
        $this->dispatch('eventsUpdated');
    }

    public function loadAllEvents()
    {
        try {
            $this->allEvents = Event::with(['institution', 'category', 'subcategories'])
                ->whereDate('data', $this->filterDate)
                ->get()
                ->groupBy('id_events_category')
                ->map(function ($group) {
                    return [
                        'category_name' => $group->first()->category->name ?? '-',
                        'events' => $group
                    ];
                });
            $this->dispatch('allEventsUpdated', $this->allEvents->toArray());
        } catch (\Exception $e) {
            $this->allEvents = collect();
            Log::error('Eroare la încărcarea tuturor evenimentelor: ' . $e->getMessage());
            session()->flash('error', 'Eroare la încărcarea tuturor evenimentelor: ' . $e->getMessage());
        }
    }

    public function updateSubcategories()
    {
        if ($this->id_events_category) {
            $this->subcategories = EventSubcategory::where('id_events_category', $this->id_events_category)->get();
        } else {
            $this->subcategories = collect();
        }
        $this->id_subcategory = [];
    }

    public function createEvent()
    {
        Log::info('createEvent apelat cu datele:', [
            'data' => $this->data,
            'id_institution' => $this->id_institution,
            'id_events_category' => $this->id_events_category,
            'id_subcategory' => $this->id_subcategory,
            'persons_involved' => $this->persons_involved,
            'events_text' => $this->events_text,
        ]);

        
        $this->data = Carbon::today()->format('Y-m-d');
        $this->id_institution = Auth::user()->institution ? Auth::user()->institution->id : null;

        
        $this->validate();

        
        if ($this->data !== Carbon::today()->format('Y-m-d')) {
            throw new \Exception('Evenimentele pot fi adăugate doar pentru ziua curentă.');
        }

        Log::info('Validare trecută cu succes');

        try {
            if (is_null($this->id_institution)) {
                throw new \Exception('Utilizatorul nu are o instituție asociată. Contactați administratorul.');
            }

            $institution = Institution::findOrFail($this->id_institution);
            $institutionName = $institution->name;

            $currentTime = Carbon::now()->format('H:i');
            $formattedEventsText = "$institutionName, $currentTime, " . ($this->events_text ?? '');

            $eventData = [
                'data' => $this->data,
                'id_institution' => $this->id_institution,
                'id_events_category' => $this->id_events_category,
                'persons_involved' => $this->persons_involved,
                'events_text' => $formattedEventsText,
                'created_at' => Carbon::now(), // Ensure created_at is set explicitly for the timer logic
            ];

            $event = Event::create($eventData);

            if (!empty($this->id_subcategory) && is_array($this->id_subcategory)) {
                $event->subcategories()->sync(array_filter($this->id_subcategory));
                Log::info('Subcategorii sincronizate:', $this->id_subcategory);
            }

            $createdAt = Carbon::parse($event->created_at)->format('d-m-Y H:i');
            $this->resetForm();
            $this->lastLoadedDate = Carbon::today()->format('Y-m-d');
            $this->loadEvents();
            $this->loadAllEvents();
            $this->dispatch('eventsUpdated');
            $this->dispatch('allEventsUpdated', $this->allEvents->toArray());
            session()->flash('message', "Eveniment creat cu succes la data: {$createdAt}");
            Log::info('Eveniment creat cu succes:', ['id' => $event->id]);
        } catch (\Exception $e) {
            Log::error('Eroare la crearea evenimentului: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            session()->flash('error', 'Eroare la crearea evenimentului: ' . $e->getMessage());
        }
    }

    public function editEvent($id)
    {
        if (!Auth::user()->can('edit events 24h')) {
            session()->flash('error', 'Nu aveți permisiunea de a edita evenimente.');
            return;
        }
        try {
            Log::info('editEvent apelat cu ID:', ['id' => $id]);
            $event = Event::with('subcategories')->findOrFail($id);
            $minutesSinceCreation = Carbon::parse($event->created_at)->diffInMinutes(Carbon::now());
            if ($minutesSinceCreation >= 5) {
                session()->flash('error', 'Perioada de 5 minute pentru editare a expirat.');
                return;
            }

            Log::info('Eveniment găsit pentru editare:', ['event' => $event->toArray(), 'type' => gettype($event)]);

            $this->editingEventId = $event->id;
            $this->data = Carbon::parse($event->data)->format('Y-m-d');
            $this->id_institution = $event->id_institution;
            $this->id_events_category = $event->id_events_category;
            $this->updateSubcategories();
            $this->id_subcategory = $event->subcategories->pluck('id')->toArray();
            $this->persons_involved = $event->persons_involved;
            $this->events_text = $event->events_text;
            $this->showModal = true;

            Log::info('Date setate pentru editare:', [
                'editingEventId' => $this->editingEventId,
                'data' => $this->data,
                'id_events_category' => $this->id_events_category,
                'id_subcategory' => $this->id_subcategory,
                'persons_involved' => $this->persons_involved,
                'events_text' => $this->events_text,
            ]);
        } catch (\Exception $e) {
            Log::error('Eroare la editarea evenimentului: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            session()->flash('error', 'Eroare la editarea evenimentului: ' . $e->getMessage());
        }
    }

    public function updateEvent()
    {
        $this->validate();

        try {
            Log::info('updateEvent apelat cu ID:', ['id' => $this->editingEventId]);
            $event = Event::findOrFail($this->editingEventId);
            $minutesSinceCreation = Carbon::parse($event->created_at)->diffInMinutes(Carbon::now());
            if ($minutesSinceCreation >= 5) {
                session()->flash('error', 'Perioada de 5 minute pentru editare a expirat.');
                $this->resetForm();
                return;
            }

            Log::info('Eveniment găsit pentru actualizare:', ['event' => $event->toArray()]);

            $event->update([
                'data' => $this->data,
                'id_events_category' => $this->id_events_category,
                'persons_involved' => $this->persons_involved,
                'events_text' => $this->events_text,
            ]);

            if (!empty($this->id_subcategory) && is_array($this->id_subcategory)) {
                $event->subcategories()->sync(array_filter($this->id_subcategory));
                Log::info('Subcategorii sincronizate:', $this->id_subcategory);
            }

            $this->resetForm();
            $this->lastLoadedDate = Carbon::today()->format('Y-m-d');
            $this->loadEvents();
            $this->loadAllEvents();
            $this->dispatch('eventsUpdated');
            $this->dispatch('allEventsUpdated', $this->allEvents->toArray());
            session()->flash('message', 'Eveniment actualizat cu succes!');
            Log::info('Eveniment actualizat cu succes:', ['id' => $event->id]);
        } catch (\Exception $e) {
            Log::error('Eroare la actualizarea evenimentului: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            session()->flash('error', 'Eroare la actualizarea evenimentului: ' . $e->getMessage());
        }
    }

    public function deleteEvent($id)
    {
        if (!Auth::user()->can('delete events 24h')) {
            session()->flash('error', 'Nu aveți permisiunea de a șterge evenimente.');
            return;
        }
        try {
            $event = Event::findOrFail($id);
            $minutesSinceCreation = Carbon::parse($event->created_at)->diffInMinutes(Carbon::now());
            if ($minutesSinceCreation >= 5) {
                session()->flash('error', 'Perioada de 5 minute pentru ștergere a expirat.');
                return;
            }

            $event->delete();
            $this->lastLoadedDate = Carbon::today()->format('Y-m-d');
            $this->loadEvents();
            $this->loadAllEvents();
            $this->dispatch('eventsUpdated');
            $this->dispatch('allEventsUpdated', $this->allEvents->toArray());
            session()->flash('message', 'Eveniment șters cu succes!');
        } catch (\Exception $e) {
            Log::error('Eroare la ștergerea evenimentului: ' . $e->getMessage());
            session()->flash('error', 'Eroare la ștergerea evenimentului: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->showModal = false;
        $this->editingEventId = null;
        $this->data = Carbon::today()->format('Y-m-d');
        $this->id_institution = Auth::user()->institution ? Auth::user()->institution->id : null;
        $this->id_events_category = null;
        $this->id_subcategory = [];
        $this->persons_involved = null;
        $this->events_text = null;
        $this->subcategories = collect();
        $this->resetErrorBag();
    }

    public function updateFilterDate()
    {
        $this->loadAllEvents();
    }

    public function render()
    {
        Log::info('Rendering events:', $this->events->toArray());
        return view('livewire.user.event-manager-24h', [
            'events' => $this->events,
            'allEvents' => $this->allEvents,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }
}